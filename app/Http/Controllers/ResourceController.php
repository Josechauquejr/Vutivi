<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;

/**
 * Expoe o catalogo compartilhado de recursos sem misturar a navegacao com o CRUD dos subtipos.
 */
class ResourceController extends Controller
{
    /**
     * Lista os recursos disponiveis no catalogo compartilhado.
     */
    public function index()
    {
        return view('index');
    }

    public function show(int $id)
    {
        $resource = Resource::with(['owner', 'physicalResource', 'digitalResource'])->findOrFail($id);

        return view('resources.show', compact('resource'));
    }

    /**
     * Exibe a página biblioteca com recursos fictícios.
     */
    public function library(Request $request)
    {
        $resources = $this->resourceQuery($request)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('library', [
            'resources' => $resources,
            'pageTitle' => 'Recursos',
            'pageEyebrow' => 'Biblioteca',
            'pageDescription' => 'Um espaco com recursos de leitura, estudo e apoio visual em vez de somente livros, com cards mais uniformes.',
        ]);
    }

    /**
     * Exibe a página Meus recursos com recursos do usuário.
     */
    public function mine(Request $request)
    {
        $resources = $this->resourceQuery($request)
            ->where('owner_id', auth()->id())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('library', [
            'resources' => $resources,
            'pageTitle' => 'Meus recursos',
            'pageEyebrow' => 'Area pessoal',
            'pageDescription' => 'Recursos cadastrados sob a sua responsabilidade.',
        ]);
    }

    /**
     * Exibe a página Favoritos com recursos escolhidos.
     */
    public function favorites(Request $request)
    {
        $resources = $this->resourceQuery($request)
            ->whereRaw('1 = 0')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('library', [
            'resources' => $resources,
            'pageTitle' => 'Recursos favoritos',
            'pageEyebrow' => 'Favoritos',
            'pageDescription' => 'Os seus recursos marcados como favoritos aparecerao aqui.',
        ]);
    }

    /**
     * Exibe recursos atualmente ligados aos emprestimos do usuario.
     */
    public function borrowed(Request $request)
    {
        $resources = $this->resourceQuery($request)
            ->whereHas('reservations', function ($query) {
                $query->where('user_id', auth()->id())->whereNull('returned_at');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('library', [
            'resources' => $resources,
            'pageTitle' => 'Recursos de emprestimo',
            'pageEyebrow' => 'Emprestimos',
            'pageDescription' => 'Recursos que estao associados aos seus emprestimos ativos.',
        ]);
    }

    /**
     * Exibe a página Categorias com recursos variados.
     */
    public function categories()
    {
        
    }

    /**
     * Exibe os detalhes completos de um recurso do catalogo.
     */
    public function destroy(int $id)
    {
        $resource = Resource::findOrFail($id);
        $resource->delete();

        return redirect()->route('resources.index')->with('success', 'Recurso excluido com sucesso.');
    }

    /**
     * Monta a consulta base do catalogo com pesquisa opcional.
     */
    private function resourceQuery(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        return Resource::with(['owner', 'physicalResource', 'digitalResource'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('owner', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            });
    }

}
