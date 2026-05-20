<?php

namespace App\Http\Controllers;

use App\Actions\Reservations\CreateReservation;
use App\Actions\Reservations\SyncResourceAvailability;
use App\Actions\Reservations\ValidateReservationAgainstResource;
use App\Http\Requests\Reservations\StoreReservationTermsRequest;
use App\Models\Reservation;
use App\Models\Resource;
use App\Models\TermAcceptance;
use App\Models\TermAndCondition;

/**
 * Gerencia a aceitação de termos antes de empréstimos de recursos físicos.
 */
class ReservationTermsController extends Controller
{
    public function __construct(
        private CreateReservation $createReservation,
        private ValidateReservationAgainstResource $validateReservationAgainstResource,
        private SyncResourceAvailability $syncResourceAvailability,
    ) {
    }

    /**
     * Exibe os termos do recurso físico antes da solicitação.
     */
    public function showTerms(Resource $resource)
    {
        if ($resource->type !== 'physical') {
            abort(404);
        }

        if ((int) $resource->owner_id === (int) auth()->id()) {
            return redirect()
                ->route('resources.show', $resource->id)
                ->with('error', 'Este recurso pertence a si. Não é possível solicitar empréstimo do próprio recurso.');
        }

        if ((int) $resource->quantity_available <= 0) {
            return redirect()
                ->route('resources.show', $resource->id)
                ->with('error', 'Sem cópias disponíveis para este recurso.');
        }

        $terms = TermAndCondition::activeByScope($resource->id, 'requisition');

        return view('reservations.terms', compact('resource', 'terms'));
    }

    /**
     * Cria a reserva depois do aceite de termos.
     */
    public function store(StoreReservationTermsRequest $request)
    {
        $resource = Resource::with('physicalResource')->findOrFail($request->resource_id);

        if ($resource->type !== 'physical') {
            abort(404);
        }

        $physicalResource = $resource->physicalResource;
        $requiresApproval = (bool) ($physicalResource?->requires_approval ?? false);

        $reservationData = [
            'resource_id' => $resource->id,
            'user_id' => auth()->id(),
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays((int) ($physicalResource?->max_loan_days ?? 7))->toDateString(),
            'status' => $requiresApproval ? Reservation::STATUS_PENDING : Reservation::STATUS_IN_USE,
            'approved_by' => $requiresApproval ? null : auth()->id(),
            'approved_at' => $requiresApproval ? null : now(),
            'picked_up_at' => $requiresApproval ? null : now(),
        ];

        $this->validateReservationAgainstResource->handle($resource, $reservationData);
        $reservation = $this->createReservation->handle($reservationData);

        TermAcceptance::create([
            'user_id' => auth()->id(),
            'resource_id' => $resource->id,
            'reservation_id' => $reservation->id,
            'term_scope' => 'requisition',
        ]);

        $this->syncResourceAvailability->handle($resource->fresh());

        $message = $requiresApproval
            ? 'Pedido de empréstimo enviado. Aguarde aprovação.'
            : 'Empréstimo aprovado. O recurso está em uso.';

        return redirect()->route('reservations.show', $reservation)->with('success', $message);
    }
}
