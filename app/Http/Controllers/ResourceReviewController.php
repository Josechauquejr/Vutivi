<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceReview;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResourceReviewController extends Controller
{
    public function store(Request $request, int $resourceId)
    {
        $resource = Resource::findOrFail($resourceId);

        $hasBorrowed = Reservation::where('user_id', auth()->id())
            ->where('resource_id', $resourceId)
            ->where('status', Reservation::STATUS_RETURNED)
            ->exists();

        if (! $hasBorrowed) {
            throw ValidationException::withMessages([
                'stars' => 'Só pode avaliar recursos que devolveu.',
            ]);
        }

        $data = $request->validate([
            'stars' => 'required|integer|min:1|max:5',
            'body'  => 'nullable|string|max:1000',
        ]);

        ResourceReview::updateOrCreate(
            ['resource_id' => $resourceId, 'user_id' => auth()->id()],
            $data,
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Avaliação guardada.']);
        }

        return back()->with('success', 'Avaliação guardada com sucesso.');
    }

    public function destroy(int $resourceId)
    {
        ResourceReview::where('resource_id', $resourceId)
            ->where('user_id', auth()->id())
            ->delete();

        return back()->with('success', 'Avaliação removida.');
    }
}
