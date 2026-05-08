<?php

namespace App\Actions\PhysicalResources;

use App\Models\Resource;
use Illuminate\Support\Facades\DB;

/**
 * Atualiza um recurso fisico mantendo sincronizados o registro base e o subtipo.
 */
class UpdatePhysicalResource
{
    /**
     * Atualiza os dados compartilhados e os detalhes do subtipo fisico.
     *
     * @param array<string, mixed> $resourceData
     * @param array<string, mixed> $physicalResourceData
     */
    public function handle(Resource $resource, array $resourceData, array $physicalResourceData): void
    {
        DB::transaction(function () use ($resource, $resourceData, $physicalResourceData) {
            $resource->update($resourceData);
            $resource->physicalResource()->update($physicalResourceData);
        });
    }
}
