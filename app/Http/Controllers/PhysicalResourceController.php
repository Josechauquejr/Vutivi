<?php

namespace App\Http\Controllers;

use App\Actions\PhysicalResources\CreatePhysicalResource;
use App\Actions\PhysicalResources\UpdatePhysicalResource;
use App\Http\Requests\PhysicalResources\StorePhysicalResourceRequest;
use App\Http\Requests\PhysicalResources\UpdatePhysicalResourceRequest;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

/**
 * Gerencia o subtipo fisico enquanto usa a tabela base de recursos para os campos compartilhados do catalogo.
 */
class PhysicalResourceController extends Controller
{
    /**
     * Injeta as acoes responsaveis por persistencia do recurso fisico.
     */
    public function __construct(
        private CreatePhysicalResource $createPhysicalResource,
        private UpdatePhysicalResource $updatePhysicalResource,
    ) {
    }

    /**
     * Lista os recursos fisicos cadastrados.
     */
    public function index()
    {
        $resources = $this->physicalResourceQuery()
            ->with(['physicalResource', 'owner'])
            ->latest()
            ->paginate(10);

        return view('physical_resources.index', compact('resources'));
    }

    /**
     * Exibe o formulario de criacao de recurso fisico.
     */
    public function create()
    {
        return view('physical_resources.create');
    }

    /**
     * Armazena um novo recurso fisico usando dados ja validados.
     */
    public function store(StorePhysicalResourceRequest $request)
    {
        $resource = $this->createPhysicalResource->handle(
            $request->resourceData(),
            $request->physicalResourceData(),
            Auth::id(),
        );

        return redirect()->route('physical-resources.show', $resource->id)
            ->with('success', 'Recurso fisico criado com sucesso.');
    }

    /**
     * Exibe os detalhes completos de um recurso fisico.
     */
    public function show(int $id)
    {
        $resource = $this->physicalResourceQuery()
            ->with(['physicalResource', 'owner', 'reservations.user'])
            ->findOrFail($id);

        return view('physical_resources.show', compact('resource'));
    }

    /**
     * Exibe o formulario de edicao de um recurso fisico.
     */
    public function edit(int $id)
    {
        $resource = $this->physicalResourceQuery()
            ->with('physicalResource')
            ->findOrFail($id);

        return view('physical_resources.edit', compact('resource'));
    }

    /**
     * Atualiza um recurso fisico usando dados ja validados.
     */
    public function update(UpdatePhysicalResourceRequest $request, int $id)
    {
        $resource = $this->physicalResourceQuery()
            ->with('physicalResource')
            ->findOrFail($id);

        $this->updatePhysicalResource->handle(
            $resource,
            $request->resourceData(),
            $request->physicalResourceData(),
        );

        return redirect()->route('physical-resources.show', $resource->id)
            ->with('success', 'Recurso fisico atualizado com sucesso.');
    }

    /**
     * Exclui um recurso fisico.
     */
    public function destroy(int $id)
    {
        $resource = $this->physicalResourceQuery()->findOrFail($id);
        $resource->delete();

        return redirect()->route('physical-resources.index')->with('success', 'Recurso fisico excluido com sucesso.');
    }

    /**
     * Retorna a query base dos recursos fisicos.
     */
    private function physicalResourceQuery()
    {
        return Resource::query()->where('type', 'physical');
    }
}
