<?php

namespace App\Http\Controllers;

use App\Actions\DigitalResources\CreateDigitalResource;
use App\Actions\DigitalResources\UpdateDigitalResource;
use App\Http\Requests\DigitalResources\StoreDigitalResourceRequest;
use App\Http\Requests\DigitalResources\UpdateDigitalResourceRequest;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

/**
 * Gerencia metadados do subtipo digital enquanto preserva o comportamento comum no recurso base.
 */
class DigitalResourceController extends Controller
{
    /**
     * Injeta as acoes responsaveis por persistencia do recurso digital.
     */
    public function __construct(
        private CreateDigitalResource $createDigitalResource,
        private UpdateDigitalResource $updateDigitalResource,
    ) {
    }

    /**
     * Lista os recursos digitais cadastrados.
     */
    public function index()
    {
        $resources = $this->digitalResourceQuery()
            ->with(['digitalResource', 'owner'])
            ->latest()
            ->paginate(10);

        return view('digital_resources.index', compact('resources'));
    }

    /**
     * Exibe o formulario de criacao de recurso digital.
     */
    public function create()
    {
        return view('digital_resources.create');
    }

    /**
     * Armazena um novo recurso digital usando dados ja validados.
     */
    public function store(StoreDigitalResourceRequest $request)
    {
        $resource = $this->createDigitalResource->handle(
            $request->resourceData(),
            $request->digitalResourceData(),
            Auth::id(),
        );

        return redirect()->route('digital-resources.show', $resource->id);
    }

    /**
     * Exibe os detalhes completos de um recurso digital.
     */
    public function show(int $id)
    {
        $resource = $this->digitalResourceQuery()
            ->with(['digitalResource', 'owner', 'reservations.user'])
            ->findOrFail($id);

        return view('digital_resources.show', compact('resource'));
    }

    /**
     * Exibe o formulario de edicao de um recurso digital.
     */
    public function edit(int $id)
    {
        $resource = $this->digitalResourceQuery()
            ->with('digitalResource')
            ->findOrFail($id);

        return view('digital_resources.edit', compact('resource'));
    }

    /**
     * Atualiza um recurso digital usando dados ja validados.
     */
    public function update(UpdateDigitalResourceRequest $request, int $id)
    {
        $resource = $this->digitalResourceQuery()
            ->with('digitalResource')
            ->findOrFail($id);

        $this->updateDigitalResource->handle(
            $resource,
            $request->resourceData(),
            $request->digitalResourceData(),
        );

        return redirect()->route('digital-resources.show', $resource->id);
    }

    /**
     * Exclui um recurso digital.
     */
    public function destroy(int $id)
    {
        $resource = $this->digitalResourceQuery()->findOrFail($id);
        $resource->delete();

        return redirect()->route('digital-resources.index');
    }

    /**
     * Retorna a query base dos recursos digitais.
     */
    private function digitalResourceQuery()
    {
        return Resource::query()->where('type', 'digital');
    }
}
