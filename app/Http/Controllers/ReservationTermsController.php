<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Resource;
use App\Models\TermAndCondition;
use App\Models\TermAcceptance;
use App\Http\Requests\Reservations\StoreReservationTermsRequest;
use App\Http\Requests\Reservations\ApproveReservationRequest;
use App\Http\Requests\Reservations\ExtendReservationRequest;

/**
 * Gerencia o fluxo de termos e condições para requisições de recursos físicos.
 * Trabalha em conjunto com ReservationController.
 */
class ReservationTermsController extends Controller
{
    /**
     * Exibe a página de termos antes de criar uma requisição.
     */
    public function showTerms(Resource $resource)
    {
        // Validar se é recurso físico
        if ($resource->type !== 'physical') {
            abort(404);
        }

        // Carregar termos de requisição
        $terms = TermAndCondition::activeByScope($resource->id, 'requisition');

        if (!$terms && !$resource->physicalResource->requires_approval) {
            // Se não há termos e é auto-approve, redirecionar direto para criação
            return redirect()->route('reservations.create-physical', ['resource' => $resource->id]);
        }

        return view('reservations.terms', compact('resource', 'terms'));
    }

    /**
     * Cria uma requisição após aceitar termos.
     */
    public function store(StoreReservationTermsRequest $request)
    {
        $resource = Resource::findOrFail($request->resource_id);

        // Validar se é recurso físico
        if ($resource->type !== 'physical') {
            abort(404);
        }

        $physicalResource = $resource->physicalResource;

        // Criar requisição
        $reservation = Reservation::create([
            'resource_id' => $resource->id,
            'user_id' => auth()->id(),
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays($physicalResource->max_loan_days),
            'status' => $physicalResource->requires_approval
                ? Reservation::STATUS_PENDING
                : Reservation::STATUS_APPROVED,
            'approved_by' => !$physicalResource->requires_approval ? auth()->id() : null,
            'approved_at' => !$physicalResource->requires_approval ? now() : null,
        ]);

        // Registrar aceitação de termos
        TermAcceptance::create([
            'user_id' => auth()->id(),
            'resource_id' => $resource->id,
            'reservation_id' => $reservation->id,
            'term_scope' => 'requisition'
        ]);

        $message = $physicalResource->requires_approval
            ? 'Requisição enviada! Aguarde aprovação.'
            : 'Requisição aprovada! Pode retirar na biblioteca.';

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', $message);
    }

    /**
     * Exibe formulário para confirmar retirada com termos.
     */
    public function showPickup(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if ($reservation->status !== Reservation::STATUS_APPROVED) {
            return back()->withErrors('Esta requisição não está pronta para retirada');
        }

        $terms = TermAndCondition::activeByScope($reservation->resource_id, 'pickup');

        return view('reservations.pickup', compact('reservation', 'terms'));
    }

    /**
     * Confirma retirada com aceitação de termos.
     */
    public function pickup(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if ($reservation->status !== Reservation::STATUS_APPROVED) {
            return back()->withErrors('Esta requisição não está pronta para retirada');
        }

        // Registrar aceitação de termos de pickup
        TermAcceptance::create([
            'user_id' => auth()->id(),
            'resource_id' => $reservation->resource_id,
            'reservation_id' => $reservation->id,
            'term_scope' => 'pickup'
        ]);

        // Atualizar status
        $reservation->update([
            'status' => Reservation::STATUS_IN_USE,
            'picked_up_at' => now()
        ]);

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Retirada confirmada!');
    }

    /**
     * Exibe formulário para estender prazo.
     */
    public function showExtend(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if (!$reservation->canExtend()) {
            return back()->withErrors('Esta requisição não pode ser estendida');
        }

        $terms = TermAndCondition::activeByScope($reservation->resource_id, 'extension');
        $maxDays = $reservation->resource->physicalResource->max_loan_days;

        return view('reservations.extend', compact('reservation', 'terms', 'maxDays'));
    }

    /**
     * Confirma extensão de prazo.
     */
    public function extend(ExtendReservationRequest $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if (!$reservation->canExtend()) {
            return back()->withErrors('Esta requisição não pode ser estendida');
        }

        $days = $request->days;

        // Registrar aceitação de termos
        TermAcceptance::create([
            'user_id' => auth()->id(),
            'resource_id' => $reservation->resource_id,
            'reservation_id' => $reservation->id,
            'term_scope' => 'extension'
        ]);

        // Atualizar data de devolução
        $reservation->update([
            'end_date' => $reservation->end_date->addDays($days),
            'extension_count' => $reservation->extension_count + 1,
            'extension_reason' => $request->reason,
            'status' => Reservation::STATUS_EXTENDED
        ]);

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', "Prazo estendido por {$days} dias!");
    }

    /**
     * Admin: Listar requisições pendentes de aprovação.
     */
    public function pending()
    {
        $reservations = Reservation::pending()
            ->with('user', 'resource', 'resource.physicalResource')
            ->latest()
            ->paginate(15);

        return view('reservations.pending', compact('reservations'));
    }

    /**
     * Admin: Aprovar uma requisição.
     */
    public function approve(ApproveReservationRequest $request, Reservation $reservation)
    {
        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return back()->withErrors('Esta requisição já foi processada');
        }

        $reservation->update([
            'status' => Reservation::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return back()->with('success', 'Requisição aprovada!');
    }

    /**
     * Admin: Rejeitar uma requisição.
     */
    public function reject(Reservation $reservation)
    {
        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return back()->withErrors('Esta requisição já foi processada');
        }

        $reservation->update([
            'status' => Reservation::STATUS_CANCELLED
        ]);

        return back()->with('success', 'Requisição cancelada');
    }

    /**
     * Admin: Marcar como devolvido.
     */
    public function return(Reservation $reservation)
    {
        if (!in_array($reservation->status, [Reservation::STATUS_IN_USE, Reservation::STATUS_EXTENDED])) {
            return back()->withErrors('Esta requisição não está em uso');
        }

        $daysOverdue = max(0, now()->diffInDays($reservation->end_date, false));

        $reservation->update([
            'status' => Reservation::STATUS_RETURNED,
            'actual_return_date' => now()->toDateString(),
            'days_overdue' => $daysOverdue,
            'returned_at' => now()
        ]);

        return back()->with('success', 'Devolução registrada!');
    }

    /**
     * Admin: Dashboard com estatísticas.
     */
    public function dashboard()
    {
        $stats = [
            'pending_count' => Reservation::pending()->count(),
            'in_use_count' => Reservation::where('status', Reservation::STATUS_IN_USE)->count(),
            'overdue_count' => Reservation::overdue()->count(),
            'extended_count' => Reservation::where('extension_count', '>', 0)->count(),
            'returned_today' => Reservation::where('status', Reservation::STATUS_RETURNED)
                ->whereDate('actual_return_date', now()->toDateString())
                ->count(),
        ];

        return view('reservations.dashboard', $stats);
    }
}
