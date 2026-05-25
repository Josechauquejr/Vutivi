<?php

namespace App\Actions\PhysicalResources;

use App\Models\Resource;
use App\Services\Moderation\ModerationScorer;
use Illuminate\Support\Facades\DB;

/**
 * Persiste um novo recurso fisico e seus detalhes em uma unica transacao.
 */
class CreatePhysicalResource
{
    public function __construct(private ModerationScorer $moderationScorer)
    {
    }

    /**
     * Cria o recurso base e o registro especifico do subtipo fisico.
     *
     * @param array<string, mixed> $resourceData
     * @param array<string, mixed> $physicalResourceData
     */
    public function handle(array $resourceData, array $physicalResourceData, int $ownerId): Resource
    {
        return DB::transaction(function () use ($resourceData, $physicalResourceData, $ownerId) {
            $resource = Resource::create($this->buildResourcePayload($resourceData, $ownerId));

            $resource->physicalResource()->create($physicalResourceData);

            return $resource;
        });
    }

    /**
     * Monta os dados do recurso base com os campos controlados pelo sistema.
     *
     * @param array<string, mixed> $resourceData
     * @return array<string, mixed>
     */
    private function buildResourcePayload(array $resourceData, int $ownerId): array
    {
        return [
            ...$resourceData,
            'type' => 'physical',
            ...$this->moderationPayload($resourceData),
            'owner_id' => $ownerId,
        ];
    }

    /**
     * @param array<string, mixed> $resourceData
     * @return array<string, mixed>
     */
    private function moderationPayload(array $resourceData): array
    {
        $analysis = $this->moderationScorer->analyze(
            (string) ($resourceData['title'] ?? ''),
            $resourceData['description'] ?? null,
        );

        return [
            'moderation_status' => $analysis['status'],
            'moderation_score' => $analysis['score'],
            'moderation_reason' => $analysis['reason'],
            'moderation_auto' => true,
        ];
    }
}
