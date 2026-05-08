<?php

namespace App\Actions\DigitalResources;

use App\Models\Resource;
use Illuminate\Support\Facades\DB;

/**
 * Persiste um novo recurso digital e seus metadados em uma unica transacao.
 */
class CreateDigitalResource
{
    /**
     * Cria o recurso base e o registro especifico do subtipo digital.
     *
     * @param array<string, mixed> $resourceData
     * @param array<string, mixed> $digitalResourceData
     */
    public function handle(array $resourceData, array $digitalResourceData, int $ownerId): Resource
    {
        return DB::transaction(function () use ($resourceData, $digitalResourceData, $ownerId) {
            $resource = Resource::create($this->buildResourcePayload($resourceData, $ownerId));

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
    private function buildResourcePayload(array $resourceData, int $ownerId): array
    {
        return [
            ...$resourceData,
            'type' => 'digital',
            'owner_id' => $ownerId,
        ];
    }
}
