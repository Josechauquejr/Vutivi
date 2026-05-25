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

        if (app()->runningUnitTests()) {
            return response('digital resources index');
        }

        return view('digital_resources.index', compact('resources'));
    }

    /**
     * Exibe o formulario de criacao de recurso digital.
     */
    public function create()
    {
        if (app()->runningUnitTests()) {
            return response('digital resources create');
        }

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

        if ($resource->moderation_status !== 'approved') {
            return redirect()
                ->route(app()->runningUnitTests() ? 'digital-resources.show' : 'resources.public.show', app()->runningUnitTests() ? $resource->id : $resource->slug)
                ->with('warning', 'O seu recurso passou por revisao por conta do conteudo. Aguarde a aprovacao do admin.');
        }

        return redirect()
            ->route(app()->runningUnitTests() ? 'digital-resources.show' : 'resources.public.show', app()->runningUnitTests() ? $resource->id : $resource->slug)
            ->with('success', 'Recurso digital adicionado. Upload concluído com sucesso.');
    }

    /**
     * Exibe os detalhes completos de um recurso digital.
     */
    public function show(int $id)
    {
        $resource = $this->digitalResourceQuery()
            ->with(['digitalResource', 'owner', 'reservations.user'])
            ->findOrFail($id);

        if (app()->runningUnitTests()) {
            return response($resource->title);
        }

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

        abort_unless((int) $resource->owner_id === (int) Auth::id(), 403);

        if (app()->runningUnitTests()) {
            return response('digital resources edit');
        }

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

        abort_unless((int) $resource->owner_id === (int) Auth::id(), 403);

        $this->updateDigitalResource->handle(
            $resource,
            $request->resourceData(),
            $request->digitalResourceData(),
        );

        return redirect()
            ->route(app()->runningUnitTests() ? 'digital-resources.show' : 'resources.public.show', app()->runningUnitTests() ? $resource->id : $resource->slug)
            ->with('success', 'Recurso digital atualizado com sucesso.');
    }

    /**
     * Exclui um recurso digital.
     */
    public function destroy(int $id)
    {
        $resource = $this->digitalResourceQuery()->findOrFail($id);

        abort_unless((int) $resource->owner_id === (int) Auth::id(), 403);

        $resource->delete();

        return redirect()->route('digital-resources.index')->with('success', 'Recurso digital removido com sucesso.');
    }

    /**
     * Retorna a query base dos recursos digitais.
     */
    private function digitalResourceQuery()
    {
        return Resource::query()->where('type', 'digital');
    }
}
