<?php

namespace App\Actions\PhysicalResources;

use App\Models\Resource;
use Illuminate\Support\Facades\DB;

/**
 * Persiste um novo recurso fisico e seus detalhes em uma unica transacao.
 */
class CreatePhysicalResource
{
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
            'owner_id' => $ownerId,
        ];
    }
}
