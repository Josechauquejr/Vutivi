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

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Exibe o formulario de criacao de reserva.
     */
    public function create()
    {
        $resources = $this->availableResourcesForForms();
        $users = $this->availableUsersForForms();

        return view('reservations.create', compact('resources', 'users'));
    }

    /**
     * Armazena uma nova reserva com dados ja validados e regras de negocio verificadas.
     */
    public function store(StoreReservationRequest $request)
    {
        $reservationData = $request->reservationData();
        $reservationData['user_id'] = auth()->id();
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

        return redirect()->route('reservations.index');
    }

    /**
     * Marca uma reserva como devolvida.
     */
    public function return(int $id)
    {
        $reservation = $this->reservation($id);

        $this->returnReservation->handle($reservation);
        $this->syncResourceAvailability->handle($reservation->resource->fresh());

        return redirect()->route('reservations.show', $reservation->id);
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
