<?php

namespace App\Http\Controllers;

use App\Actions\Reservations\IssueFine;
use App\Actions\Reservations\ReturnReservation;
use App\Actions\Reservations\SyncResourceAvailability;
use App\Models\Fine;
use App\Models\Reservation;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LibrarianController extends Controller
{
    public function __construct(
        private ReturnReservation $returnReservation,
        private SyncResourceAvailability $syncResourceAvailability,
        private IssueFine $issueFine,
    ) {}

    public function dashboard()
    {
        $pending = Reservation::with(['resource:id,title,type,slug', 'user:id,name,email'])
            ->where('status', Reservation::STATUS_PENDING)
            ->latest()
            ->get();

        $awaitingPickup = Reservation::with(['resource:id,title,type,slug', 'user:id,name,email'])
            ->where('status', Reservation::STATUS_APPROVED)
            ->latest('approved_at')
            ->get();

        $activeLoans = Reservation::with(['resource:id,title,type,slug', 'user:id,name,email'])
            ->whereIn('status', Reservation::ACTIVE_LOAN_STATUSES)
            ->orderBy('end_date')
            ->get();

        $overdueLoans = Reservation::with(['resource:id,title,type,slug', 'user:id,name,email'])
            ->overdue()
            ->orderBy('end_date')
            ->get();

        $unpaidFines = Fine::with([
            'reservation.resource:id,title,slug',
            'user:id,name,email',
        ])
            ->whereNull('paid_at')
            ->latest()
            ->get();

        $stats = [
            'pending'         => $pending->count(),
            'awaiting_pickup' => $awaitingPickup->count(),
            'active'          => $activeLoans->count(),
            'overdue'         => $overdueLoans->count(),
            'unpaid_fines'    => (float) $unpaidFines->sum('total'),
        ];

        return view('librarian.dashboard', compact(
            'pending',
            'awaitingPickup',
            'activeLoans',
            'overdueLoans',
            'unpaidFines',
            'stats',
        ));
    }

    public function confirmPickup(int $id)
    {
        $reservation = Reservation::findOrFail($id);
        abort_unless($reservation->status === Reservation::STATUS_APPROVED, 422);

        $reservation->update([
            'status'     => Reservation::STATUS_IN_USE,
            'picked_up_at' => now(),
        ]);

        return back()->with('success', 'Levantamento confirmado. Recurso em uso.');
    }

    public function confirmReturn(int $id)
    {
        $reservation = Reservation::with('resource')->findOrFail($id);
        abort_unless(
            in_array($reservation->status, Reservation::ACTIVE_LOAN_STATUSES, true),
            422,
        );

        $this->returnReservation->handle($reservation);
        $this->syncResourceAvailability->handle($reservation->resource->fresh());
        $this->issueFine->handle($reservation->fresh());

        return back()->with('success', 'Devolução confirmada com sucesso.');
    }

    public function approvePending(int $id)
    {
        $reservation = Reservation::with('resource')->findOrFail($id);
        abort_unless($reservation->status === Reservation::STATUS_PENDING, 422);

        DB::transaction(function () use ($reservation) {
            $resource = Resource::whereKey($reservation->resource_id)->lockForUpdate()->firstOrFail();

            if ((int) $resource->quantity_available <= 0) {
                abort(422, 'Sem cópias disponíveis para este recurso.');
            }

            $resource->decrement('quantity_available');
            $resource->refresh();
            $resource->update(['status' => (int) $resource->quantity_available > 0 ? 'available' : 'reserved']);

            $goDirectlyInUse = $resource->type === 'digital';

            $reservation->update(array_filter([
                'status'      => $goDirectlyInUse ? Reservation::STATUS_IN_USE : Reservation::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'picked_up_at' => $goDirectlyInUse ? now() : null,
            ], fn ($v) => $v !== null));
        });

        return back()->with('success', 'Pedido aprovado. Aguarda levantamento na biblioteca.');
    }

    public function denyPending(int $id)
    {
        $reservation = Reservation::with('resource')->findOrFail($id);
        abort_unless($reservation->status === Reservation::STATUS_PENDING, 422);

        $reservation->update(['status' => Reservation::STATUS_DENIED]);
        $this->syncResourceAvailability->handle($reservation->resource->fresh());

        return back()->with('error', 'Pedido recusado.');
    }

    public function markFinePaid(int $id)
    {
        $fine = Fine::findOrFail($id);
        abort_if($fine->paid_at !== null, 422);

        $fine->update(['paid_at' => now()]);

        return back()->with('success', 'Multa marcada como paga.');
    }
}
