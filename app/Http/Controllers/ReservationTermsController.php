<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reservations\StoreReservationTermsRequest;
use App\Models\Reservation;
use App\Models\Resource;
use App\Models\TermAcceptance;
use App\Models\TermAndCondition;

/**
 * Gerencia a aceitacao de termos antes de emprestimos de recursos fisicos.
 */
class ReservationTermsController extends Controller
{
    /**
     * Exibe os termos do recurso fisico antes da solicitacao.
     */
    public function showTerms(Resource $resource)
    {
        if ($resource->type !== 'physical') {
            abort(404);
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

        $reservation = Reservation::create([
            'resource_id' => $resource->id,
            'user_id' => auth()->id(),
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays((int) ($physicalResource?->max_loan_days ?? 7))->toDateString(),
            'status' => $requiresApproval ? Reservation::STATUS_PENDING : Reservation::STATUS_APPROVED,
            'approved_by' => $requiresApproval ? null : auth()->id(),
            'approved_at' => $requiresApproval ? null : now(),
        ]);

        TermAcceptance::create([
            'user_id' => auth()->id(),
            'resource_id' => $resource->id,
            'reservation_id' => $reservation->id,
            'term_scope' => 'requisition',
        ]);

        $message = $requiresApproval
            ? 'Pedido de empréstimo enviado. Aguarde aprovação.'
            : 'Pedido de empréstimo aceite. Pode retirar o recurso na biblioteca.';

        return redirect()->route('reservations.show', $reservation)->with('success', $message);
    }
}
