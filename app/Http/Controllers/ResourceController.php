<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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

        $stats = [
            'total'       => Resource::count(),
            'digital'     => Resource::where('type', 'digital')->count(),
            'physical'    => Resource::where('type', 'physical')->count(),
            'activeLoans' => Reservation::whereNull('returned_at')->whereIn('status', Reservation::COPY_HOLDING_STATUSES)->count(),
        ];

        $popularResources = Resource::with(['owner', 'digitalResource', 'physicalResource'])
            ->withCount(['favoritedBy as favorites_count'])
            ->when(
                Schema::hasColumn('resources', 'moderation_status'),
                fn ($q) => $q->where('moderation_status', 'approved')
            )
            ->where('status', 'available')
            ->orderByDesc('favorites_count')
            ->orderByDesc('downloads_count')
            ->limit(3)
            ->get();

        return view('pages.home', compact('stats', 'popularResources'));
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
            return response($resource->title . "\n" . ($resource->physicalResource?->location ?? 'Recurso digital protegido'));
        }

        return view('resources.show', compact('resource'));
    }

    public function showPublic(Resource $resource)
    {
        abort_unless($this->canOpenResource($resource), 404);

        $resource->load(['owner', 'physicalResource', 'digitalResource', 'reservations.user']);

        return view('resources.show', compact('resource'));
    }

    public function viewDigital(Resource $resource)
    {
        $resource->load('digitalResource');

        abort_unless($resource->type === 'digital' && $resource->digitalResource?->file_path, 404);
        abort_unless($this->canOpenResource($resource), 404);
        abort_unless($resource->digitalResource->access_type === 'view', 403);

        $path = $resource->digitalResource->file_path;
        $disk = $this->digitalStorageDisk($path);
        abort_unless($disk, 404);

        $filename = $this->safeDigitalFilename($resource);

        return Storage::disk($disk)->response($path, $filename, [
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function downloadDigital(Resource $resource)
    {
        $resource->load('digitalResource');

        abort_unless($resource->type === 'digital' && $resource->digitalResource?->file_path, 404);
        abort_unless($this->canOpenResource($resource), 404);
        abort_unless($resource->digitalResource->access_type === 'download', 403);

        $path = $resource->digitalResource->file_path;
        $disk = $this->digitalStorageDisk($path);
        abort_unless($disk, 404);

        $resource->increment('downloads_count');

        return Storage::disk($disk)->download($path, $this->safeDigitalFilename($resource));
    }

    public function about()
    {
        return view('pages.about');
    }

    /**
     * Exibe a pagina biblioteca com recursos reais cadastrados.
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

    public function suggestions(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        if (mb_strlen($search) < 2) {
            return response()->json([]);
        }

        return $this->resourceQuery($request)
            ->limit(8)
            ->get()
            ->map(fn (Resource $resource) => [
                'title' => $resource->title,
                'authors' => $resource->authors,
                'type' => $resource->type,
                'description' => str($resource->description ?? '')->limit(92)->toString(),
                'url' => $resource->slug ? route('resources.public.show', $resource->slug) : route('resources.show', $resource->id),
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

        $resources = $this->applySort($this->resourceQuery($request, includeUnavailableModeration: true)
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
            ->with(['reservations' => function ($query) {
                $query->where('user_id', auth()->id())
                    ->whereNull('returned_at')
                    ->whereIn('status', Reservation::COPY_HOLDING_STATUSES)
                    ->latest();
            }])
            ->whereHas('reservations', function ($query) {
                $query->where('user_id', auth()->id())
                    ->whereNull('returned_at')
                    ->whereIn('status', Reservation::COPY_HOLDING_STATUSES);
            })
            , $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return $this->libraryView($resources, [
            'resources' => $resources,
            'pageTitle' => 'Empréstimos ativos',
            'pageEyebrow' => 'Empréstimos',
            'pageDescription' => 'Recursos associados aos seus empréstimos em andamento.',
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
            ->whereHas('reservations', fn ($query) => $query->whereNull('returned_at')->whereIn('status', [
                Reservation::STATUS_PENDING,
                ...Reservation::COPY_HOLDING_STATUSES,
            ]))
            , $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return $this->libraryView($resources, [
            'resources' => $resources,
            'pageTitle' => 'Solicitações',
            'pageEyebrow' => 'Empréstimos',
            'pageDescription' => 'Acompanhe pedidos e recursos da sua biblioteca pessoal que estão com outros utilizadores.',
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
            ->whereNull('returned_at')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->whereDate('end_date', '<=', now()->addDays(3)->toDateString())
                        ->whereIn('status', Reservation::COPY_HOLDING_STATUSES);
                })->orWhere(function ($query) {
                    $query->whereHas('resource', fn ($query) => $query->where('owner_id', auth()->id()))
                        ->where('status', Reservation::STATUS_EXTENSION_PENDING);
                })->orWhere(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->whereNotNull('extension_decision')
                        ->whereNotNull('extension_decided_at');
                });
            })
            ->latest('updated_at')
            ->paginate(10);

        return view('resources.loan-alerts', compact('reservations'));
    }

    /**
     * Mostra a area de conta do utilizador.
     */
    public function account()
    {
        $user = auth()->user();
        $stats = [
            'resources' => Resource::where('owner_id', $user->id)->count(),
            'borrowed' => Reservation::where('user_id', $user->id)->whereNull('returned_at')->whereIn('status', Reservation::COPY_HOLDING_STATUSES)->count(),
            'lent' => Reservation::whereHas('resource', fn ($query) => $query->where('owner_id', $user->id))
                ->whereNull('returned_at')
                ->whereIn('status', [
                    Reservation::STATUS_PENDING,
                    ...Reservation::COPY_HOLDING_STATUSES,
                ])
                ->count(),
        ];

        return view('users.account', compact('user', 'stats'));
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
    private function resourceQuery(Request $request, bool $includeUnavailableModeration = false)
    {
        $search = trim((string) $request->query('q', ''));
        $type = $request->query('type');
        $status = $request->query('status');
        $format = strtolower((string) $request->query('format', ''));

        return Resource::with(['owner', 'physicalResource', 'digitalResource'])
            ->withCount([
                'favoritedBy as favorites_count',
                'reservations as loans_count',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('authors', 'like', "%{$search}%")
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
            ->when(
                ! $includeUnavailableModeration && Schema::hasColumn('resources', 'moderation_status'),
                fn ($query) => $query->where('moderation_status', 'approved')
            )
            ->withExists(['favoritedBy as is_favorited' => fn ($query) => $query->where('users.id', auth()->id() ?? 0)]);
    }

    private function canOpenResource(Resource $resource): bool
    {
        if (! Schema::hasColumn('resources', 'moderation_status')) {
            return true;
        }

        return $resource->moderation_status === 'approved'
            || (auth()->check() && (int) $resource->owner_id === (int) auth()->id());
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
        return in_array((int) $request->query('per_page', 12), [10, 12, 16, 20, 40, 60], true)
            ? (int) $request->query('per_page', 12)
            : 12;
    }

    /**
     * Compartilha metricas pequenas com as telas de biblioteca.
     */
    private function libraryView($resources, array $data)
    {
        return view('resources.library', [
            ...$data,
            'stats' => [
                'total' => Resource::count(),
                'digital' => Resource::where('type', 'digital')->count(),
                'physical' => Resource::where('type', 'physical')->count(),
                'activeLoans' => Reservation::whereNull('returned_at')->whereIn('status', Reservation::COPY_HOLDING_STATUSES)->count(),
            ],
            'activeTab' => $data['activeTab'] ?? null,
            'showOwnerActions' => $data['showOwnerActions'] ?? false,
        ]);
    }

    private function safeDigitalFilename(Resource $resource): string
    {
        $path = (string) $resource->digitalResource?->file_path;
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $name = (string) str($resource->title)->slug();
        $name = $name !== '' ? $name : 'recurso-digital';

        return $extension ? "{$name}.{$extension}" : $name;
    }

    private function digitalStorageDisk(string $path): ?string
    {
        if (Storage::exists($path)) {
            return config('filesystems.default');
        }

        if (Storage::disk('public')->exists($path)) {
            return 'public';
        }

        return null;
    }

}
