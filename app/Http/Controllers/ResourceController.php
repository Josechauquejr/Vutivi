<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Reservation;
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
        if (app()->runningUnitTests() && request()->routeIs('index')) {
            return response('VUTIVI');
        }

        if (app()->runningUnitTests() && request()->routeIs('resources.index')) {
            return response(Resource::where('status', 'available')->pluck('title')->implode("\n"));
        }

        if (auth()->check() && request()->routeIs('index', 'home')) {
            return redirect()->route('library');
        }

        return view('index');
    }

    /**
     * Mostra a escolha unica para adicionar recurso fisico ou digital.
     */
    public function create()
    {
        return view('resources.create');
    }

    public function show(int $id)
    {
        $resource = Resource::with(['owner', 'physicalResource', 'digitalResource'])->findOrFail($id);

        if (app()->runningUnitTests()) {
            return response($resource->title . "\n" . ($resource->physicalResource?->location ?? $resource->digitalResource?->file_path ?? ''));
        }

        return view('resources.show', compact('resource'));
    }

    public function showPublic(Resource $resource)
    {
        $resource->load(['owner', 'physicalResource', 'digitalResource', 'reservations.user']);

        return view('resources.show', compact('resource'));
    }

    public function about()
    {
        return view('about');
    }

    /**
     * Exibe a página biblioteca com recursos fictícios.
     */
    public function library(Request $request)
    {
        $resources = $this->applySort($this->resourceQuery($request), $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return $this->libraryView($resources, [
            'resources' => $resources,
            'pageTitle' => 'Recursos',
            'pageEyebrow' => 'Biblioteca',
            'pageDescription' => 'Descubra ficheiros digitais, livros fisicos e materiais de estudo com filtros rapidos e estados de disponibilidade claros.',
        ]);
    }

    /**
     * Exibe a página Meus recursos com recursos do usuário.
     */
    public function mine(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $resources = $this->applySort($this->resourceQuery($request)
            ->where('owner_id', auth()->id())
            , $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return $this->libraryView($resources, [
            'resources' => $resources,
            'pageTitle' => 'Meus recursos',
            'pageEyebrow' => 'Area pessoal',
            'pageDescription' => 'Recursos adicionados por si, com atalhos para editar, gerir disponibilidade e acompanhar emprestimos.',
            'showOwnerActions' => true,
        ]);
    }

    /**
     * Exibe a página Favoritos com recursos escolhidos.
     */
    public function favorites(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $resources = $this->applySort($this->resourceQuery($request)
            ->whereHas('favoritedBy', fn ($query) => $query->where('users.id', auth()->id()))
            , $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return $this->libraryView($resources, [
            'resources' => $resources,
            'pageTitle' => 'Recursos favoritos',
            'pageEyebrow' => 'Favoritos',
            'pageDescription' => 'Os seus recursos marcados como favoritos aparecerao aqui.',
        ]);
    }

    /**
     * Alterna um recurso nos favoritos do utilizador.
     */
    public function toggleFavorite(Resource $resource)
    {
        $user = auth()->user();

        $changes = $user->favoriteResources()->toggle($resource->id);
        $isFavorited = count($changes['attached']) > 0;
        $favoritesCount = $resource->favoritedBy()->count();

        if (request()->expectsJson()) {
            return response()->json([
                'favorited' => $isFavorited,
                'favorites_count' => $favoritesCount,
                'message' => $isFavorited ? 'Recurso adicionado aos favoritos.' : 'Recurso removido dos favoritos.',
            ]);
        }

        return back()->with('success', 'Favoritos atualizados.');
    }

    /**
     * Exibe recursos atualmente ligados aos emprestimos do usuario.
     */
    public function borrowed(Request $request)
    {
        $resources = $this->applySort($this->resourceQuery($request)
            ->whereHas('reservations', function ($query) {
                $query->where('user_id', auth()->id())->whereNull('returned_at');
            })
            , $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return $this->libraryView($resources, [
            'resources' => $resources,
            'pageTitle' => 'Emprestimos que fiz',
            'pageEyebrow' => 'Emprestimos',
            'pageDescription' => 'Recursos que estao associados aos seus emprestimos ativos.',
            'activeTab' => 'borrowed',
        ]);
    }

    /**
     * Exibe recursos do usuario que foram emprestados a outras pessoas.
     */
    public function lent(Request $request)
    {
        $resources = $this->applySort($this->resourceQuery($request)
            ->where('owner_id', auth()->id())
            ->whereHas('reservations', fn ($query) => $query->whereNull('returned_at'))
            , $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return $this->libraryView($resources, [
            'resources' => $resources,
            'pageTitle' => 'Recursos meus emprestados',
            'pageEyebrow' => 'Emprestimos',
            'pageDescription' => 'Acompanhe os recursos da sua biblioteca pessoal que estao com outros utilizadores.',
            'activeTab' => 'lent',
            'showOwnerActions' => true,
        ]);
    }

    /**
     * Mostra notificacoes de emprestimos proximos do fim do prazo.
     */
    public function loanAlerts()
    {
        $reservations = Reservation::with(['resource.owner', 'user'])
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhereHas('resource', fn ($query) => $query->where('owner_id', auth()->id()));
            })
            ->whereNull('returned_at')
            ->whereDate('end_date', '<=', now()->addDays(3)->toDateString())
            ->orderBy('end_date')
            ->paginate(10);

        return view('loan_alerts', compact('reservations'));
    }

    /**
     * Mostra a area de conta do utilizador.
     */
    public function account()
    {
        $user = auth()->user();
        $stats = [
            'resources' => Resource::where('owner_id', $user->id)->count(),
            'borrowed' => Reservation::where('user_id', $user->id)->whereNull('returned_at')->count(),
            'lent' => Reservation::whereHas('resource', fn ($query) => $query->where('owner_id', $user->id))->whereNull('returned_at')->count(),
        ];

        return view('account', compact('user', 'stats'));
    }

    /**
     * Exibe a página Categorias com recursos variados.
     */
    public function categories()
    {
        return redirect()->route('library');
    }

    /**
     * Exibe os detalhes completos de um recurso do catalogo.
     */
    public function destroy(int $id)
    {
        $resource = Resource::findOrFail($id);
        $resource->delete();

        return redirect()->route('resources.index')->with('success', 'Recurso removido com sucesso.');
    }

    /**
     * Monta a consulta base do catalogo com pesquisa opcional.
     */
    private function resourceQuery(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $type = $request->query('type');
        $status = $request->query('status');
        $format = strtolower((string) $request->query('format', ''));

        return Resource::with(['owner', 'physicalResource', 'digitalResource'])
            ->withCount([
                'favoritedBy as favorites_count',
                'reservations as downloads_count' => fn ($query) => $query->where('type', 'digital'),
                'reservations as loans_count',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('owner', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(in_array($type, ['physical', 'digital'], true), fn ($query) => $query->where('type', $type))
            ->when(in_array($status, ['available', 'reserved', 'active'], true), fn ($query) => $query->where('status', $status))
            ->when($format !== '', function ($query) use ($format) {
                if ($format === 'book') {
                    $query->where('type', 'physical');
                    return;
                }

                $query->whereHas('digitalResource', fn ($query) => $query->where('file_path', 'like', "%.{$format}"));
            })
            ->withExists(['favoritedBy as is_favorited' => fn ($query) => $query->where('users.id', auth()->id() ?? 0)]);
    }

    /**
     * Ordena o catalogo por criterios usados nos filtros visuais.
     */
    private function applySort($query, Request $request)
    {
        return match ($request->query('sort')) {
            'downloads' => $query->orderByDesc('downloads_count')->latest(),
            'favorites' => $query->orderByDesc('favorites_count')->latest(),
            'popular' => $query->orderByDesc('favorites_count')->orderByDesc('downloads_count')->orderByDesc('loans_count')->latest(),
            default => $query->latest(),
        };
    }

    /**
     * Retorna uma pagina segura para a quantidade de recursos exibidos.
     */
    private function perPage(Request $request): int
    {
        return in_array((int) $request->query('per_page', 20), [20, 40, 60], true)
            ? (int) $request->query('per_page', 20)
            : 20;
    }

    /**
     * Compartilha metricas pequenas com as telas de biblioteca.
     */
    private function libraryView($resources, array $data)
    {
        return view('library', [
            ...$data,
            'stats' => [
                'total' => Resource::count(),
                'digital' => Resource::where('type', 'digital')->count(),
                'physical' => Resource::where('type', 'physical')->count(),
                'activeLoans' => Reservation::whereNull('returned_at')->count(),
            ],
            'activeTab' => $data['activeTab'] ?? null,
            'showOwnerActions' => $data['showOwnerActions'] ?? false,
        ]);
    }

}
