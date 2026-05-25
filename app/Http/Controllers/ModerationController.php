<?php

namespace App\Http\Controllers;

use App\Models\BlacklistHash;
use App\Models\BlacklistWord;
use App\Models\ModerationNotification;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ModerationController extends Controller
{
    private array $blacklistWords = [];
    private array $blacklistHashes = [];
    private array $blacklistWordEntries = [];

    public function index()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        abort_unless(auth()->user()?->isAdmin(), 403);

        return view('moderation.index');
    }

    public function state()
    {
        $this->authorizeAdmin();
        $this->loadBlacklist();

        $resources = Resource::with(['owner', 'digitalResource', 'physicalResource', 'moderator'])
            ->latest()
            ->get()
            ->map(fn (Resource $resource) => $this->resourcePayload($resource));

        $users = User::withCount('resources')
            ->latest()
            ->get()
            ->map(fn (User $user) => $this->userPayload($user));

        return response()->json([
            'resources' => $resources,
            'users' => $users,
            'stats' => [
                'resources' => $resources->count(),
                'users' => $users->count(),
                'digital' => $resources->where('type', 'digital')->count(),
                'physical' => $resources->where('type', 'physical')->count(),
            ],
            'blacklist' => [
                'words' => $this->blacklistWordsPayload(),
                'hashes' => BlacklistHash::with('createdBy')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(fn (BlacklistHash $hash) => [
                        'id' => $hash->id,
                        'hash' => $hash->hash,
                        'reason' => $hash->reason,
                        'created_by' => $hash->created_by,
                        'created_by_name' => $hash->createdBy?->name,
                        'created_at' => $hash->created_at?->toISOString(),
                    ]),
            ],
            'activity' => $this->activityPayload(),
        ]);
    }

    public function users(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['nullable', 'string', 'in:admin,superadmin'],
        ]);

        $username = $data['username'] ?: Str::slug($data['name']).'-'.Str::lower(Str::random(4));

        $user = User::create([
            'name' => $data['name'],
            'username' => $username,
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'] ?? 'admin',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Utilizador criado com sucesso.',
            'user' => $this->userPayload($user),
        ], 201);
    }

    public function updateUser(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'string', 'in:admin,superadmin'],
        ]);

        $payload = collect($data)->except('password')->all();
        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);

        return response()->json([
            'ok' => true,
            'message' => 'Administrador atualizado com sucesso.',
            'user' => $this->userPayload($user->fresh()->loadCount('resources')),
        ]);
    }

    public function destroyUser(User $user)
    {
        $this->authorizeAdmin();
        abort_if((int) $user->id === (int) auth()->id(), 422, 'Nao pode remover a propria conta neste painel.');
        abort_unless($user->isAdmin(), 422, 'Este painel remove apenas administradores.');

        $user->delete();

        return response()->json(['ok' => true]);
    }

    public function updateMe(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore(auth()->id())],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(auth()->id())],
            'password' => ['nullable', 'string', 'min:6'],
            'profile_photo' => ['nullable', 'image', 'max:4096'],
        ]);

        $user = auth()->user();
        $payload = collect($data)->except(['password', 'profile_photo'])->all();
        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }
        if ($request->hasFile('profile_photo')) {
            $payload['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($payload);

        return response()->json([
            'ok' => true,
            'message' => 'Conta atualizada com sucesso.',
            'user' => $this->userPayload($user->fresh()->loadCount('resources')),
        ]);
    }

    public function moderate(Request $request, Resource $resource)
    {
        $this->authorizeAdmin();
        $resource->load('owner');

        $data = $request->validate([
            'status' => ['required', 'in:approved,review,rejected'],
            'score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'auto' => ['nullable', 'boolean'],
        ]);

        if (! Schema::hasColumn('resources', 'moderation_status')) {
            return response()->json([
                'ok' => false,
                'message' => 'Execute as migrations para gravar decisões de moderação.',
            ], 409);
        }

        $status = $data['status'];
        $reason = $data['reason'] ?? null;
        $resourceTitle = $resource->title;
        $ownerId = $resource->owner_id;

        return DB::transaction(function () use ($resource, $data, $status, $reason, $resourceTitle, $ownerId) {
            $resource->update([
                'moderation_status' => $status,
                'moderation_score' => $data['score'] ?? $resource->moderation_score ?? 0,
                'moderation_reason' => $reason,
                'moderated_by' => auth()->id(),
                'moderated_at' => now(),
                'moderation_auto' => (bool) ($data['auto'] ?? false),
            ]);

            if ($ownerId) {
                $this->notifyResourceOwner($ownerId, $resourceTitle, $status, $reason);
            }

            if ($status === 'rejected') {
                $resource->delete();

                return response()->json([
                    'ok' => true,
                    'deleted' => true,
                    'resource_id' => $resource->id,
                    'message' => 'Recurso rejeitado, removido e comunicado ao utilizador.',
                ]);
            }

            return response()->json([
                'ok' => true,
                'resource' => $this->resourcePayload($resource->fresh(['owner', 'digitalResource', 'physicalResource', 'moderator'])),
            ]);
        });
    }

    public function banUser(Request $request, User $user)
    {
        $this->authorizeAdmin();
        abort_if((int) $user->id === (int) auth()->id(), 422, 'Nao pode suspender a propria conta.');
        abort_if($user->isAdmin(), 422, 'Administradores nao podem ser suspensos por este painel.');

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $user->update([
            'banned_at' => now(),
            'ban_reason' => $data['reason'] ?: 'Conta suspensa por conduta imprudente no envio de recursos.',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Utilizador suspenso com sucesso.',
            'user' => $this->userPayload($user->fresh()->loadCount('resources')),
        ]);
    }

    public function blacklistWords()
    {
        $this->authorizeAdmin();

        return response()->json([
            'ok' => true,
            'words' => $this->blacklistWordsPayload(),
        ]);
    }

    public function blacklistHashes()
    {
        $this->authorizeAdmin();

        return response()->json([
            'ok' => true,
            'hashes' => BlacklistHash::with('createdBy')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn (BlacklistHash $hash) => [
                    'id' => $hash->id,
                    'hash' => $hash->hash,
                    'reason' => $hash->reason,
                    'created_by' => $hash->created_by,
                    'created_by_name' => $hash->createdBy?->name,
                    'created_at' => $hash->created_at?->toISOString(),
                ]),
        ]);
    }

    public function storeBlacklistWord(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'word' => ['required', 'string', 'max:255', 'unique:blacklist_words,word'],
            'category' => ['required', 'string', 'max:100'],
            'severity' => ['required', 'string', 'in:low,medium,high,critical'],
        ]);

        $schema = $this->blacklistWordSchema();
        $payload = [
            'word' => Str::lower(trim($data['word'])),
            'created_by' => auth()->id(),
        ];

        if ($schema['category']) {
            $payload['category'] = trim($data['category']);
        }

        if ($schema['severity']) {
            $payload['severity'] = $data['severity'];
        }

        $word = BlacklistWord::create($payload);

        return response()->json([
            'ok' => true,
            'word' => $this->blacklistWordPayload($word->fresh('createdBy'), $schema),
        ], 201);
    }

    public function destroyBlacklistWord(BlacklistWord $blacklistWord)
    {
        $this->authorizeAdmin();
        $blacklistWord->delete();

        return response()->json([
            'ok' => true,
        ]);
    }

    public function updateBlacklistWord(Request $request, BlacklistWord $blacklistWord)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'word' => ['required', 'string', 'max:255', Rule::unique('blacklist_words', 'word')->ignore($blacklistWord->id)],
            'category' => ['required', 'string', 'max:100'],
            'severity' => ['required', 'string', 'in:low,medium,high,critical'],
        ]);

        $schema = $this->blacklistWordSchema();
        $payload = [
            'word' => Str::lower(trim($data['word'])),
        ];

        if ($schema['category']) {
            $payload['category'] = trim($data['category']);
        }

        if ($schema['severity']) {
            $payload['severity'] = $data['severity'];
        }

        $blacklistWord->update($payload);

        return response()->json([
            'ok' => true,
            'word' => $this->blacklistWordPayload($blacklistWord->fresh('createdBy'), $schema),
        ]);
    }

    public function storeBlacklistHash(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'hash' => ['required', 'string', 'max:255', 'unique:blacklist_hashes,hash'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $hash = BlacklistHash::create([
            'hash' => Str::lower(trim($data['hash'])),
            'reason' => $data['reason'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'ok' => true,
            'hash' => $hash,
        ], 201);
    }

    public function destroyBlacklistHash(BlacklistHash $blacklistHash)
    {
        $this->authorizeAdmin();
        $blacklistHash->delete();

        return response()->json([
            'ok' => true,
        ]);
    }

    public function updateBlacklistHash(Request $request, BlacklistHash $blacklistHash)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'hash' => ['required', 'string', 'max:255', Rule::unique('blacklist_hashes', 'hash')->ignore($blacklistHash->id)],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $blacklistHash->update([
            'hash' => Str::lower(trim($data['hash'])),
            'reason' => $data['reason'] ?? null,
        ]);

        return response()->json(['ok' => true, 'hash' => $blacklistHash->fresh('createdBy')]);
    }

    public function preview(Resource $resource)
    {
        $this->authorizeAdmin();
        $resource->load('digitalResource');

        abort_unless($resource->type === 'digital' && $resource->digitalResource?->file_path, 404);

        $path = $resource->digitalResource->file_path;
        $disk = $this->digitalStorageDisk($path);
        abort_unless($disk !== null, 404);

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk($disk);
        $filename = $this->safeDigitalFilename($resource);
        $headers = $this->previewHeaders($path, $filename);

        if (config("filesystems.disks.{$disk}.driver") === 'local') {
            return response()->file($storage->path($path), $headers);
        }

        return $storage->response($path, $filename, $headers, 'inline');
    }

    private function resourcePayload(Resource $resource): array
    {
        $type = $resource->type;
        $filePath = $resource->digitalResource?->file_path;
        $extension = $filePath ? pathinfo($filePath, PATHINFO_EXTENSION) : ($type === 'physical' ? 'book' : 'digital');
        $score = Schema::hasColumn('resources', 'moderation_score')
            ? (int) ($resource->moderation_score ?? $this->scoreForResource($resource))
            : $this->scoreForResource($resource);
        $status = Schema::hasColumn('resources', 'moderation_status')
            ? ($resource->moderation_status ?: $this->statusForScore($score))
            : $this->statusForScore($score);

        return [
            'id' => $resource->id,
            'title' => $resource->title,
            'description' => $resource->description,
            'authors' => $resource->authors,
            'type' => $type,
            'format' => strtolower($extension ?: $type),
            'uploader' => $resource->owner?->name ?? 'Sem dono',
            'owner_id' => $resource->owner_id,
            'owner_email' => $resource->owner?->email,
            'size' => $this->resourceSize($resource),
            'score' => $score,
            'status' => $status,
            'library_status' => $resource->status,
            'auto' => Schema::hasColumn('resources', 'moderation_auto') ? (bool) $resource->moderation_auto : true,
            'createdAt' => $resource->created_at?->toISOString(),
            'moderatedBy' => $resource->moderator?->name ?? (Schema::hasColumn('resources', 'moderated_by') && $resource->moderated_by ? 'Moderador' : ''),
            'hash' => $resource->digitalResource?->file_hash ?: $this->fallbackHash($resource),
            'preview_url' => $this->previewUrl($resource),
            'media_kind' => $this->mediaKind(strtolower($extension ?: '')),
            'motivo' => Schema::hasColumn('resources', 'moderation_reason') ? $resource->moderation_reason : $this->reasonFor($score),
            'checks' => [
                'mime' => true,
                'blacklisted' => $this->resourceIsBlacklisted($resource),
                'extension' => true,
            ],
            'detected' => $this->detectedKeywords($resource),
        ];
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'banned_at' => $user->banned_at?->toISOString(),
            'ban_reason' => $user->ban_reason,
            'profile_photo' => $user->profile_photo,
            'profile_photo_url' => $user->profile_photo ? $this->publicStorage()->url($user->profile_photo) : null,
            'resources_count' => $user->resources_count ?? $user->resources()->count(),
            'created_at' => $user->created_at?->toISOString(),
            'updated_at' => $user->updated_at?->toISOString(),
        ];
    }

    private function activityPayload(): array
    {
        $resources = Resource::with(['owner', 'moderator'])
            ->latest('updated_at')
            ->limit(12)
            ->get()
            ->map(fn (Resource $resource) => [
                'id' => 'resource-'.$resource->id,
                'timestamp' => $resource->updated_at?->toISOString(),
                'admin' => $resource->moderator?->name ?? $resource->owner?->name ?? 'Sistema',
                'action' => match ($resource->moderation_status) {
                    'approved' => 'Recurso aprovado',
                    'rejected' => 'Recurso rejeitado',
                    'review' => 'Recurso em revisao',
                    default => 'Recurso atualizado',
                },
                'resource' => $resource->title,
                'result' => $resource->moderation_status ?: 'review',
            ]);

        $hashes = BlacklistHash::with('createdBy')
            ->latest('updated_at')
            ->limit(8)
            ->get()
            ->map(fn (BlacklistHash $hash) => [
                'id' => 'hash-'.$hash->id,
                'timestamp' => $hash->updated_at?->toISOString(),
                'admin' => $hash->createdBy?->name ?? 'Moderacao',
                'action' => 'Blacklist de hash atualizada',
                'resource' => $hash->hash,
                'result' => 'rejected',
            ]);

        $words = BlacklistWord::with('createdBy')
            ->latest('updated_at')
            ->limit(8)
            ->get()
            ->map(fn (BlacklistWord $word) => [
                'id' => 'word-'.$word->id,
                'timestamp' => $word->updated_at?->toISOString(),
                'admin' => $word->createdBy?->name ?? 'Moderacao',
                'action' => 'Palavra-chave atualizada',
                'resource' => $word->word.' / '.$word->severity,
                'result' => $word->severity === 'critical' ? 'rejected' : 'review',
            ]);

        $admins = User::whereIn('role', ['admin', 'superadmin'])
            ->latest('updated_at')
            ->limit(8)
            ->get()
            ->map(fn (User $user) => [
                'id' => 'admin-'.$user->id,
                'timestamp' => $user->updated_at?->toISOString(),
                'admin' => $user->name,
                'action' => 'Administrador atualizado',
                'resource' => $user->email,
                'result' => 'approved',
            ]);

        return $resources
            ->concat($hashes)
            ->concat($words)
            ->concat($admins)
            ->sortByDesc('timestamp')
            ->take(30)
            ->values()
            ->all();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check(), 401);
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    private function notifyResourceOwner(int $ownerId, string $title, string $status, ?string $reason): void
    {
        if (! Schema::hasTable('moderation_notifications')) {
            return;
        }

        $approved = $status === 'approved';

        ModerationNotification::create([
            'user_id' => $ownerId,
            'resource_title' => $title,
            'status' => $status,
            'message' => $approved
                ? "O recurso {$title} foi aprovado e ja esta disponivel na biblioteca."
                : "O recurso {$title} nao esta disponivel. Motivo: ".($reason ?: 'reprovado pela moderacao.'),
        ]);
    }

    private function resourceIsBlacklisted(Resource $resource): bool
    {
        $hash = Str::lower($resource->digitalResource?->file_hash ?: $resource->digitalResource?->file_path ?: $this->fallbackHash($resource));

        if ($hash && in_array($hash, $this->blacklistHashes, true)) {
            return true;
        }

        $text = Str::lower($resource->title.' '.$resource->description.' '.($resource->digitalResource?->file_hash ?? ''));

        foreach ($this->blacklistWords as $word) {
            if ($word !== '' && str_contains($text, $word)) {
                return true;
            }
        }

        return false;
    }

    private function loadBlacklist(): void
    {
        $columns = ['word'];
        $schema = $this->blacklistWordSchema();

        if ($schema['category']) {
            $columns[] = 'category';
        }

        if ($schema['severity']) {
            $columns[] = 'severity';
        }

        $this->blacklistWordEntries = BlacklistWord::get($columns)
            ->map(fn ($entry) => [
                'word' => Str::lower($entry->word),
                'category' => $schema['category'] ? ($entry->category ?: 'geral') : 'geral',
                'severity' => $schema['severity'] ? ($entry->severity ?: 'high') : 'high',
            ])
            ->all();

        $this->blacklistWords = collect($this->blacklistWordEntries)
            ->pluck('word')
            ->filter()
            ->all();

        $this->blacklistHashes = BlacklistHash::pluck('hash')
            ->map(fn ($hash) => Str::lower($hash))
            ->filter()
            ->all();
    }

    private function blacklistWordsPayload()
    {
        $schema = $this->blacklistWordSchema();
        $query = BlacklistWord::with('createdBy');

        if ($schema['category']) {
            $query->orderBy('category');
        }

        return $query
            ->orderBy('word')
            ->get()
            ->map(fn (BlacklistWord $word) => $this->blacklistWordPayload($word, $schema));
    }

    private function blacklistWordPayload(BlacklistWord $word, array $schema): array
    {
        return [
            'id' => $word->id,
            'word' => $word->word,
            'category' => $schema['category'] ? ($word->category ?: 'Geral') : 'Geral',
            'severity' => $schema['severity'] ? ($word->severity ?: 'high') : 'high',
            'created_by' => $word->created_by,
            'created_by_name' => $word->createdBy?->name,
        ];
    }

    private function blacklistWordSchema(): array
    {
        return [
            'category' => Schema::hasColumn('blacklist_words', 'category'),
            'severity' => Schema::hasColumn('blacklist_words', 'severity'),
        ];
    }

    private function previewUrl(Resource $resource): ?string
    {
        return $resource->type === 'digital' && $resource->digitalResource?->file_path
            ? route('moderation.resources.preview', $resource->id)
            : null;
    }

    private function mediaKind(string $extension): string
    {
        return match ($extension) {
            'pdf' => 'pdf',
            'mp4', 'mov', 'webm' => 'video',
            'mp3', 'wav', 'ogg', 'm4a' => 'audio',
            default => 'other',
        };
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

    private function publicStorage(): FilesystemAdapter
    {
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        return $storage;
    }

    private function safeDigitalFilename(Resource $resource): string
    {
        $path = (string) $resource->digitalResource?->file_path;
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $name = (string) str($resource->title)->slug();
        $name = $name !== '' ? $name : 'recurso-digital';

        return $extension ? "{$name}.{$extension}" : $name;
    }

    private function previewHeaders(string $path, string $filename): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $contentType = match ($extension) {
            'pdf' => 'application/pdf',
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            'webm' => 'video/webm',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4',
            default => 'application/octet-stream',
        };

        return [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ];
    }

    private function scoreForResource(Resource $resource): int
    {
        return $this->scoreFor([
            'titulo' => $resource->title,
            'conteudo_texto' => $resource->description,
            'hash' => $resource->digitalResource?->file_hash ?: $resource->digitalResource?->file_path,
        ]);
    }

    private function statusForScore(int $score): string
    {
        return $score <= 30 ? 'approved' : ($score >= 80 ? 'rejected' : 'review');
    }

    private function fallbackHash(Resource $resource): string
    {
        return hash('sha256', $resource->id.'|'.$resource->title.'|'.$resource->created_at);
    }

    private function resourceSize(Resource $resource): string
    {
        $path = $resource->digitalResource?->file_path;

        if (! $path || ! is_file(storage_path('app/'.$path))) {
            return $resource->type === 'physical' ? ((int) $resource->quantity_available).' copias' : 'N/D';
        }

        $bytes = filesize(storage_path('app/'.$path));

        return $bytes >= 1048576
            ? round($bytes / 1048576, 1).' MB'
            : round($bytes / 1024, 1).' KB';
    }

    private function detectedKeywords(Resource $resource): array
    {
        $text = Str::lower($resource->title.' '.$resource->description);
        $keywords = [
            'explosivo' => ['severity' => 'critical', 'category' => 'Explosivos'],
            'bomba' => ['severity' => 'critical', 'category' => 'Explosivos'],
            'arma' => ['severity' => 'high', 'category' => 'Armas'],
            'droga' => ['severity' => 'high', 'category' => 'Drogas'],
            'quimico' => ['severity' => 'medium', 'category' => 'Químicos'],
            'veneno' => ['severity' => 'critical', 'category' => 'Químicos'],
        ];

        foreach ($this->blacklistWordEntries as $entry) {
            if (! empty($entry['word'])) {
                $keywords[$entry['word']] = [
                    'severity' => $entry['severity'],
                    'category' => $entry['category'],
                ];
            }
        }

        return collect($keywords)
            ->filter(fn ($metadata, $word) => str_contains($text, $word))
            ->map(fn ($metadata, $word) => [
                'word' => $word,
                'severity' => $metadata['severity'],
                'category' => $metadata['category'],
            ])
            ->values()
            ->all();
    }

    private function scoreFor(array $payload): int
    {
        $text = Str::lower(($payload['titulo'] ?? '').' '.($payload['conteudo_texto'] ?? '').' '.($payload['hash'] ?? ''));
        $score = (crc32($text) % 38) + 8;

        foreach (['arma', 'explosivo', 'droga', 'quimico', 'malware', 'bomba', 'veneno'] as $word) {
            if (str_contains($text, $word)) {
                $score += 18;
            }
        }

        foreach (['critical', 'blacklist', 'proibido'] as $word) {
            if (str_contains($text, $word)) {
                $score += 32;
            }
        }

        foreach ($this->blacklistWords as $word) {
            if ($word !== '' && str_contains($text, $word)) {
                $score += 36;
            }
        }

        $payloadHash = Str::lower(trim((string) ($payload['hash'] ?? '')));

        foreach ($this->blacklistHashes as $hash) {
            if ($hash !== '' && $payloadHash !== '' && hash_equals($hash, $payloadHash)) {
                $score = max($score + 70, 95);
            } elseif ($hash !== '' && str_contains($text, $hash)) {
                $score += 52;
            }
        }

        return min(100, $score);
    }

    private function reasonFor(int $score): string
    {
        if ($score <= 30) {
            return 'Nenhuma ameaca detectada';
        }

        if ($score >= 80) {
            return 'Risco critico detectado pelo pipeline automatico';
        }

        return 'Conteudo suspeito requer revisao humana';
    }
}
