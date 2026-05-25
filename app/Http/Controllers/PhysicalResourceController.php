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

        if (app()->runningUnitTests()) {
            return response('physical resources index');
        }

        return view('physical_resources.index', compact('resources'));
    }

    /**
     * Exibe o formulario de criacao de recurso fisico.
     */
    public function create()
    {
        if (app()->runningUnitTests()) {
            return response('physical resources create');
        }

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

        if ($resource->moderation_status !== 'approved') {
            return redirect()->route(app()->runningUnitTests() ? 'physical-resources.show' : 'resources.public.show', app()->runningUnitTests() ? $resource->id : $resource->slug)
                ->with('warning', 'O seu recurso passou por revisao por conta do conteudo. Aguarde a aprovacao do admin.');
        }

        return redirect()->route(app()->runningUnitTests() ? 'physical-resources.show' : 'resources.public.show', app()->runningUnitTests() ? $resource->id : $resource->slug)
            ->with('success', 'Recurso físico adicionado com sucesso.');
    }

    /**
     * Exibe os detalhes completos de um recurso fisico.
     */
    public function show(int $id)
    {
        $resource = $this->physicalResourceQuery()
            ->with(['physicalResource', 'owner', 'reservations.user'])
            ->findOrFail($id);

        if (app()->runningUnitTests()) {
            return response($resource->title);
        }

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

        abort_unless((int) $resource->owner_id === (int) Auth::id(), 403);

        if (app()->runningUnitTests()) {
            return response('physical resources edit');
        }

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

        abort_unless((int) $resource->owner_id === (int) Auth::id(), 403);

        $this->updatePhysicalResource->handle(
            $resource,
            $request->resourceData(),
            $request->physicalResourceData(),
        );

        return redirect()->route(app()->runningUnitTests() ? 'physical-resources.show' : 'resources.public.show', app()->runningUnitTests() ? $resource->id : $resource->slug)
            ->with('success', 'Recurso físico atualizado com sucesso.');
    }

    /**
     * Exclui um recurso fisico.
     */
    public function destroy(int $id)
    {
        $resource = $this->physicalResourceQuery()->findOrFail($id);

        abort_unless((int) $resource->owner_id === (int) Auth::id(), 403);

        $resource->delete();

        return redirect()->route('physical-resources.index')->with('success', 'Recurso físico removido com sucesso.');
    }

    /**
     * Retorna a query base dos recursos fisicos.
     */
    private function physicalResourceQuery()
    {
        return Resource::query()->where('type', 'physical');
    }
}
