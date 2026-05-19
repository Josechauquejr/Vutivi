<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Termos e Condições para recursos físicos.
 * Cada recurso pode ter múltiplos termos em diferentes escopos (requisição, retirada, extensão, devolução).
 */
class TermAndCondition extends Model
{
    protected $table = 'terms_and_conditions';

    protected $fillable = [
        'resource_id',
        'scope',
        'title',
        'content',
        'version',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Retorna o recurso associado a este termo.
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * Obtém o termo ativo para um escopo específico.
     */
    public static function activeByScope($resourceId, $scope)
    {
        return self::where('resource_id', $resourceId)
            ->where('scope', $scope)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Obtém todos os termos ativos de um recurso.
     */
    public static function activeForResource($resourceId)
    {
        return self::where('resource_id', $resourceId)
            ->where('is_active', true)
            ->get();
    }
}
