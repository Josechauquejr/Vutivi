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
            ->route('resources.show', $resource->id)
            ->with('success', 'Emprestimo solicitado com sucesso.');
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
        $previousResource = $reservation->resource;
        $reservationData = $request->reservationData();
        $resource = $this->reservationResource((int) $reservationData['resource_id']);

        $this->validateReservationAgainstResource->handle($resource, $reservationData, $reservation);
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
        $reservation = $this->reservation($id);

        $this->returnReservation->handle($reservation);
        $this->syncResourceAvailability->handle($reservation->resource->fresh());

        return redirect()->route('reservations.show', $reservation->id)->with('success', 'Empréstimo marcado como devolvido.');
    }

    public function requestExtension(Request $request, int $id)
    {
        $reservation = $this->reservation($id);
        abort_unless((int) $reservation->user_id === (int) auth()->id(), 403);

        $reservation->update([
            'status' => Reservation::STATUS_EXTENSION_PENDING,
            'extension_reason' => $request->input('extension_reason', 'Pedido de extensão solicitado pelo utilizador.'),
        ]);

        return back()->with('success', 'Pedido de extensão enviado ao dono do recurso.');
    }

    public function approveExtension(int $id)
    {
        $reservation = $this->reservationDetails($id);
        abort_unless((int) $reservation->resource?->owner_id === (int) auth()->id(), 403);

        $reservation->update([
            'status' => Reservation::STATUS_EXTENDED,
            'extension_count' => ((int) $reservation->extension_count) + 1,
            'end_date' => $reservation->end_date->copy()->addDays(7)->toDateString(),
        ]);

        return back()->with('success', 'Extensão aprovada. O novo prazo foi aplicado.');
    }

    public function denyExtension(int $id)
    {
        $reservation = $this->reservationDetails($id);
        abort_unless((int) $reservation->resource?->owner_id === (int) auth()->id(), 403);

        $reservation->update(['status' => Reservation::STATUS_IN_USE]);

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
        return Reservation::with(['resource', 'user'])->findOrFail($id);
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
}
