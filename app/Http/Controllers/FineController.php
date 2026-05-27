<?php

namespace App\Http\Controllers;

use App\Models\Fine;

class FineController extends Controller
{
    public function index()
    {
        $fines = Fine::where('user_id', auth()->id())
            ->with('reservation.resource:id,title,slug')
            ->latest()
            ->paginate(15);

        $unpaidTotal = Fine::where('user_id', auth()->id())->whereNull('paid_at')->sum('total');

        return view('fines.index', compact('fines', 'unpaidTotal'));
    }
}
