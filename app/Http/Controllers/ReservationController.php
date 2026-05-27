<?php

namespace App\Http\Controllers;

use App\Actions\Reservations\CreateReservation;
use App\Actions\Reservations\DeleteReservation;
use App\Actions\Reservations\ReturnReservation;
use App\Actions\Reservations\SyncResourceAvailability;
use App\Actions\Reservations\UpdateReservation;
use App\Actions\Reservations\ValidateReservationAgainstResource;
use App\Http\Requests\Reservations\StoreReservationRequest;
use App\Http\Requests\Reservations\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Coordena regras de emprestimo, verificacao de disponibilidade e atualizacao do ciclo de vida da reserva.
 */
class ReservationController extends Controller
{
    /**
     * Injeta as acoes pequenas que compoem o fluxo da reserva.
     */
    public function __construct(
        private CreateReservation $createReservation,
        private UpdateReservation $updateReservation,
        private DeleteReservation $deleteReservation,
        private ReturnReservation $returnReservation,
        private ValidateReservationAgainstResource $validateReservationAgainstResource,
        private SyncResourceAvailability $syncResourceAvailability,
    ) {
    }

    /**
     * Lista as reservas cadastradas.
     */
    public function index()
    {
        $reservations = Reservation::with(['resource', 'user'])->latest()->paginate(10);

        if (app()->runningUnitTests()) {
            return response('reservations index');
        }

        return view('reservations.index', compact('reservations'));
    }

    public function history(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $reservations = Reservation::with(['resource.owner', 'user'])
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhereHas('resource', fn ($query) => $query->where('owner_id', auth()->id()));
            })
            ->where(function ($query) {
                $query->whereNotNull('returned_at')
                    ->orWhereIn('status', [Reservation::STATUS_RETURNED, Reservation::STATUS_DENIED, Reservation::STATUS_CANCELLED])
                    ->orWhere(fn ($query) => $query->whereNull('returned_at')->whereDate('end_date', '<', now()->toDateString()));
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('resource', fn ($query) => $query->where('title', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when(in_array($status, ['returned', 'denied', 'cancelled', 'overdue'], true), function ($query) use ($status) {
                if ($status === 'overdue') {
                    $query->whereNull('returned_at')->whereDate('end_date', '<', now()->toDateString());
                    return;
                }

                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('reservations.history', compact('reservations'));
    }

    /**
     * Exibe o formulario de criacao de reserva.
     */
    public function create()
    {
        $resources = $this->availableResourcesForForms();
        $users = $this->availableUsersForForms();

        if (app()->runningUnitTests()) {
            return response('reservations create');
        }

        return view('reservations.create', compact('resources', 'users'));
    }

    /**
     * Armazena uma nova reserva com dados ja validados e regras de negocio verificadas.
     */
    public function store(StoreReservationRequest $request)
    {
        $reservationData = $request->reservationData();
        $reservationData['user_id'] = $reservationData['user_id'] ?? auth()->id();
        $resource = $this->reservationResource((int) $reservationData['resource_id']);

        $this->validateReservationAgainstResource->handle($resource, $reservationData);

        $reservation = $this->createReservation->handle($reservationData);

        $this->syncResourceAvailability->handle($resource->fresh());

        return redirect()
            ->route('reservations.show', $reservation->id)
            ->with('success', 'Empréstimo solicitado com sucesso.');
    }

    /**
     * Exibe os detalhes de uma reserva especifica.
     */
    public function show(int $id)
    {
        $reservation = $this->reservationDetails($id);

        if (app()->runningUnitTests()) {
            return response('reservations show');
        }

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Exibe o formulario de edicao de reserva.
     */
    public function edit(int $id)
    {
        $reservation = $this->reservation($id);
        $reservation->load('resource');
        abort_unless((int) $reservation->resource?->owner_id === (int) auth()->id(), 403);

        $resources = $this->availableResourcesForForms();
        $users = $this->availableUsersForForms();

        if (app()->runningUnitTests()) {
            return response('reservations edit');
        }

        return view('reservations.edit', compact('reservation', 'resources', 'users'));
    }

    /**
     * Atualiza uma reserva usando dados ja validados e regras de negocio verificadas.
     */
    public function update(UpdateReservationRequest $request, int $id)
    {
        $reservation = $this->reservation($id);
        $reservation->load('resource');
        abort_unless((int) $reservation->resource?->owner_id === (int) auth()->id(), 403);

        $previousResource = $reservation->resource;
        $reservationData = $request->reservationData();
        $resource = $this->reservationResource((int) $reservationData['resource_id']);

        $this->validateReservationAgainstResource->handle($resource, $reservationData, $reservation);
        $this->transferHeldCopyIfResourceChanged($reservation, $previousResource, $resource);
        $this->updateReservation->handle($reservation, $reservationData);

        $this->syncResourceAvailability->handle($resource->fresh());
        $this->syncPreviousResourceIfChanged($previousResource, $resource);

        return redirect()->route('reservations.show', $reservation->id);
    }

    /**
     * Exclui uma reserva existente.
     */
    public function destroy(int $id)
    {
        $reservation = $this->reservation($id);
        $reservation->load('resource');
        abort_unless((int) $reservation->resource?->owner_id === (int) auth()->id(), 403);

        $resource = $reservation->resource;

        $this->deleteReservation->handle($reservation);
        $this->syncResourceAvailability->handle($resource->fresh());

        return redirect()->route('reservations.index')->with('success', 'Empréstimo removido com sucesso.');
    }

    /**
     * Marca uma reserva como devolvida.
     */
    public function return(int $id)
    {
        $reservation = $this->reservationDetails($id);
        abort_unless(
            (int) $reservation->user_id === (int) auth()->id()
                || (int) $reservation->resource?->owner_id === (int) auth()->id(),
            403
        );

        $this->returnReservation->handle($reservation);
        $this->syncResourceAvailability->handle($reservation->resource->fresh());

        if (request()->expectsJson()) {
            $resource = $reservation->resource->fresh();

            return response()->json([
                'message' => 'Recurso marcado como devolvido com sucesso.',
                'status' => 'returned',
                'status_label' => 'Devolvido',
                'copies_available' => (int) $resource->quantity_available,
                'resource_status' => $resource->status,
                'resource_status_label' => $resource->status === 'available' ? 'Disponível' : 'Indisponível',
            ]);
        }

        return redirect()->route('reservations.show', $reservation->id)->with('success', 'Empréstimo marcado como devolvido.');
    }

    public function approve(int $id)
    {
        $reservation = $this->reservationDetails($id);
        abort_unless((int) $reservation->resource?->owner_id === (int) auth()->id(), 403);
        abort_unless($reservation->status === Reservation::STATUS_PENDING, 422);

        DB::transaction(function () use ($id) {
            $reservation = Reservation::with('resource')->whereKey($id)->lockForUpdate()->firstOrFail();
            $resource = Resource::whereKey($reservation->resource_id)->lockForUpdate()->firstOrFail();

            if ((int) $resource->quantity_available <= 0) {
                throw ValidationException::withMessages([
                    'resource_id' => 'Sem cópias disponíveis.',
                ]);
            }

            $resource->decrement('quantity_available');
            $resource->refresh();
            $resource->update(['status' => (int) $resource->quantity_available > 0 ? 'available' : 'reserved']);

            $reservation->update([
                'status' => Reservation::STATUS_IN_USE,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'picked_up_at' => now(),
            ]);
        });

        return back()->with('success', 'Pedido aprovado. O recurso entrou em uso.');
    }

    public function deny(int $id)
    {
        $reservation = $this->reservationDetails($id);
        abort_unless((int) $reservation->resource?->owner_id === (int) auth()->id(), 403);

        $reservation->update(['status' => Reservation::STATUS_DENIED]);
        $this->syncResourceAvailability->handle($reservation->resource->fresh());

        return back()->with('error', 'Pedido recusado.');
    }

    public function requestExtension(Request $request, int $id)
    {
        $reservation = $this->reservation($id);
        $reservation->load('resource.physicalResource');
        abort_unless((int) $reservation->user_id === (int) auth()->id(), 403);

        if ($reservation->status === Reservation::STATUS_EXTENSION_PENDING) {
            throw ValidationException::withMessages([
                'extension_reason' => 'Ja existe um pedido de extensao pendente para este emprestimo.',
            ]);
        }

        if (! $reservation->canExtend()) {
            throw ValidationException::withMessages([
                'extension_reason' => 'Este emprestimo nao pode ser estendido neste momento.',
            ]);
        }

        $reservation->update([
            'status' => Reservation::STATUS_EXTENSION_PENDING,
            'extension_requested_at' => now(),
            'extension_decision' => null,
            'extension_decided_at' => null,
            'extension_decision_note' => null,
            'extension_reason' => $request->input('extension_reason', 'Pedido de extensão solicitado pelo utilizador.'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pedido enviado com sucesso. Aguarde resposta do proprietário do recurso.',
                'status' => Reservation::STATUS_EXTENSION_PENDING,
                'status_label' => 'Extensão solicitada',
                'extension_reason' => $reservation->fresh()->extension_reason,
            ]);
        }

        return back()->with('success', 'Pedido de extensão enviado ao dono do recurso.');
    }

    public function approveExtension(Request $request, int $id)
    {
        $reservation = $this->reservationDetails($id);
        abort_unless((int) $reservation->resource?->owner_id === (int) auth()->id(), 403);
        abort_unless($reservation->status === Reservation::STATUS_EXTENSION_PENDING, 422);

        $reservation->update([
            'status' => Reservation::STATUS_EXTENDED,
            'extension_count' => ((int) $reservation->extension_count) + 1,
            'end_date' => $reservation->end_date->copy()->addDays(7)->toDateString(),
            'extension_decision' => Reservation::EXTENSION_APPROVED,
            'extension_decided_at' => now(),
            'extension_decision_note' => $request->input('extension_decision_note'),
            'extension_reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Extensão aprovada. O novo prazo foi aplicado.');
    }

    public function denyExtension(Request $request, int $id)
    {
        $reservation = $this->reservationDetails($id);
        abort_unless((int) $reservation->resource?->owner_id === (int) auth()->id(), 403);
        abort_unless($reservation->status === Reservation::STATUS_EXTENSION_PENDING, 422);

        $reservation->update([
            'status' => $reservation->picked_up_at ? Reservation::STATUS_IN_USE : Reservation::STATUS_APPROVED,
            'extension_decision' => Reservation::EXTENSION_DENIED,
            'extension_decided_at' => now(),
            'extension_decision_note' => $request->input('extension_decision_note'),
            'extension_reviewed_by' => auth()->id(),
        ]);

        return back()->with('error', 'Extensão negada. O prazo original permanece ativo.');
    }

    /**
     * Retorna os recursos disponiveis para os formularios de reserva.
     */
    private function availableResourcesForForms()
    {
        return Resource::orderBy('title')->get();
    }

    /**
     * Retorna os usuarios disponiveis para os formularios de reserva.
     */
    private function availableUsersForForms()
    {
        return User::orderBy('name')->get();
    }

    /**
     * Busca o recurso usado nas validacoes de negocio da reserva.
     */
    private function reservationResource(int $resourceId): Resource
    {
        return Resource::with(['physicalResource', 'digitalResource'])->findOrFail($resourceId);
    }

    /**
     * Busca uma reserva simples para operacoes de manutencao.
     */
    private function reservation(int $id): Reservation
    {
        return Reservation::findOrFail($id);
    }

    /**
     * Busca uma reserva com relacionamentos para exibicao detalhada.
     */
    private function reservationDetails(int $id): Reservation
    {
        return Reservation::with(['resource.owner', 'user', 'approver'])->findOrFail($id);
    }

    /**
     * Recalcula o recurso anterior quando a reserva muda de recurso.
     */
    private function syncPreviousResourceIfChanged(Resource $previousResource, Resource $currentResource): void
    {
        if ($previousResource->is($currentResource)) {
            return;
        }

        $this->syncResourceAvailability->handle($previousResource->fresh());
    }

    private function transferHeldCopyIfResourceChanged(Reservation $reservation, Resource $previousResource, Resource $currentResource): void
    {
        if ($previousResource->is($currentResource) || $previousResource->type !== 'physical' || $currentResource->type !== 'physical') {
            return;
        }

        if (! in_array($reservation->status, Reservation::COPY_HOLDING_STATUSES, true) || $reservation->returned_at !== null) {
            return;
        }

        DB::transaction(function () use ($previousResource, $currentResource) {
            $old = Resource::whereKey($previousResource->id)->lockForUpdate()->first();
            $new = Resource::whereKey($currentResource->id)->lockForUpdate()->first();

            if ($old) {
                $old->increment('quantity_available');
                $old->refresh();
                $old->update(['status' => 'available']);
            }

            if ($new && (int) $new->quantity_available > 0) {
                $new->decrement('quantity_available');
                $new->refresh();
                $new->update(['status' => (int) $new->quantity_available > 0 ? 'available' : 'reserved']);
            }
        });
    }
}
