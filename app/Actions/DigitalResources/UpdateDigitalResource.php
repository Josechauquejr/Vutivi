<?php

namespace App\Actions\DigitalResources;

use App\Models\Resource;
use Illuminate\Support\Facades\DB;

/**
 * Atualiza um recurso digital mantendo sincronizados o registro base e o subtipo.
 */
class UpdateDigitalResource
{
    /**
     * Atualiza os dados compartilhados e os detalhes do subtipo digital.
     *
     * @param array<string, mixed> $resourceData
     * @param array<string, mixed> $digitalResourceData
     */
    public function handle(Resource $resource, array $resourceData, array $digitalResourceData): void
    {
        DB::transaction(function () use ($resource, $resourceData, $digitalResourceData) {
            $resource->update($resourceData);
            $resource->digitalResource()->update($digitalResourceData);
        });
    }
}
