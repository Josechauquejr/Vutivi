<?php

namespace App\Actions\DigitalResources;

use App\Models\Resource;
use App\Services\Moderation\ModerationScorer;
use Illuminate\Support\Facades\DB;

/**
 * Persiste um novo recurso digital e seus metadados em uma unica transacao.
 */
class CreateDigitalResource
{
    public function __construct(private ModerationScorer $moderationScorer)
    {
    }

    /**
     * Cria o recurso base e o registro especifico do subtipo digital.
     *
     * @param array<string, mixed> $resourceData
     * @param array<string, mixed> $digitalResourceData
     */
    public function handle(array $resourceData, array $digitalResourceData, int $ownerId): Resource
    {
        return DB::transaction(function () use ($resourceData, $digitalResourceData, $ownerId) {
            $resource = Resource::create($this->buildResourcePayload($resourceData, $digitalResourceData, $ownerId));

            $resource->digitalResource()->create($digitalResourceData);

            return $resource;
        });
    }

    /**
     * Monta os dados do recurso base com os campos controlados pelo sistema.
     *
     * @param array<string, mixed> $resourceData
     * @return array<string, mixed>
     */
    private function buildResourcePayload(array $resourceData, array $digitalResourceData, int $ownerId): array
    {
        return [
            ...$resourceData,
            'type' => 'digital',
            ...$this->moderationPayload($resourceData, $digitalResourceData),
            'owner_id' => $ownerId,
        ];
    }

    /**
     * @param array<string, mixed> $resourceData
     * @param array<string, mixed> $digitalResourceData
     * @return array<string, mixed>
     */
    private function moderationPayload(array $resourceData, array $digitalResourceData): array
    {
        $analysis = $this->moderationScorer->analyze(
            (string) ($resourceData['title'] ?? ''),
            $resourceData['description'] ?? null,
            $digitalResourceData['file_hash'] ?? $digitalResourceData['file_path'] ?? null,
        );

        return [
            'moderation_status' => $analysis['status'],
            'moderation_score' => $analysis['score'],
            'moderation_reason' => $analysis['reason'],
            'moderation_auto' => true,
        ];
    }
}
