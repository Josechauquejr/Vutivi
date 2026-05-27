<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Reservation;
use App\Models\Resource;
use App\Models\ResourceReview;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalBorrowed = Reservation::where('user_id', $user->id)
            ->whereIn('status', [Reservation::STATUS_RETURNED, ...Reservation::COPY_HOLDING_STATUSES])
            ->count();

        $totalReturned = Reservation::where('user_id', $user->id)
            ->where('status', Reservation::STATUS_RETURNED)
            ->count();

        $activeLoans = Reservation::where('user_id', $user->id)
            ->whereNull('returned_at')
            ->whereIn('status', Reservation::COPY_HOLDING_STATUSES)
            ->with('resource')
            ->get();

        $overdueLoans = Reservation::where('user_id', $user->id)
            ->whereNull('returned_at')
            ->whereIn('status', [Reservation::STATUS_IN_USE, Reservation::STATUS_EXTENDED])
            ->whereDate('end_date', '<', now()->toDateString())
            ->count();

        $totalDaysOverdue = Reservation::where('user_id', $user->id)
            ->whereNotNull('actual_return_date')
            ->whereColumn('actual_return_date', '>', 'end_date')
            ->selectRaw("SUM(actual_return_date::date - end_date::date) as total")
            ->value('total') ?? 0;

        $unpaidFines = Fine::where('user_id', $user->id)
            ->whereNull('paid_at')
            ->sum('total');

        $topBorrowed = Reservation::where('user_id', $user->id)
            ->select('resource_id', DB::raw('count(*) as borrow_count'))
            ->with('resource:id,title,type,cover_image,slug')
            ->groupBy('resource_id')
            ->orderByDesc('borrow_count')
            ->limit(5)
            ->get();

        $recentReviews = ResourceReview::where('user_id', $user->id)
            ->with('resource:id,title,cover_image,slug')
            ->latest()
            ->limit(5)
            ->get();

        $ownedResources = Resource::where('owner_id', $user->id)->count();

        $pendingRequests = Reservation::whereHas('resource', fn ($q) => $q->where('owner_id', $user->id))
            ->where('status', Reservation::STATUS_PENDING)
            ->count();

        return view('dashboard', compact(
            'user',
            'totalBorrowed',
            'totalReturned',
            'activeLoans',
            'overdueLoans',
            'totalDaysOverdue',
            'unpaidFines',
            'topBorrowed',
            'recentReviews',
            'ownedResources',
            'pendingRequests',
        ));
    }
}
