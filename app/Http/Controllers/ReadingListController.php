<?php

namespace App\Http\Controllers;

use App\Models\ReadingList;
use App\Models\ReadingListItem;
use Illuminate\Http\Request;

class ReadingListController extends Controller
{
    public function index()
    {
        $lists = ReadingList::where('user_id', auth()->id())
            ->withCount('items')
            ->with(['items.resource:id,title,cover_image,type,slug,status'])
            ->orderBy('name')
            ->get();

        return view('reading-lists.index', compact('lists'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);

        $list = ReadingList::create([
            'user_id' => auth()->id(),
            'name'    => $data['name'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['id' => $list->id, 'name' => $list->name]);
        }

        return back()->with('success', 'Lista criada.');
    }

    public function destroy(int $id)
    {
        ReadingList::where('id', $id)->where('user_id', auth()->id())->delete();
        return back()->with('success', 'Lista eliminada.');
    }

    public function addItem(Request $request, int $listId)
    {
        $list = ReadingList::where('id', $listId)->where('user_id', auth()->id())->firstOrFail();

        $data = $request->validate(['resource_id' => 'required|integer|exists:resources,id']);

        ReadingListItem::firstOrCreate([
            'reading_list_id' => $list->id,
            'resource_id'     => $data['resource_id'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Recurso adicionado à lista.']);
        }

        return back()->with('success', 'Recurso adicionado.');
    }

    public function removeItem(int $listId, int $resourceId)
    {
        ReadingListItem::whereHas('list', fn ($q) => $q->where('user_id', auth()->id()))
            ->where('reading_list_id', $listId)
            ->where('resource_id', $resourceId)
            ->delete();

        return back()->with('success', 'Recurso removido da lista.');
    }

    public function ensureDefaults(): void
    {
        $existing = ReadingList::where('user_id', auth()->id())->pluck('name')->toArray();

        foreach (ReadingList::DEFAULT_LISTS as $name) {
            if (! in_array($name, $existing, true)) {
                ReadingList::create(['user_id' => auth()->id(), 'name' => $name]);
            }
        }
    }
}
