<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * DigitalResource separa os metadados de acesso do catalogo compartilhado de recursos.
 */
class DigitalResource extends Model
{
    protected $fillable = [
        'resource_id',
        'file_path',
        'access_type',
        'access_days',
    ];

    // Essas linhas espelham detalhes do catalogo e hoje nao participam de uma trilha temporal de auditoria.
    public $timestamps = false;

    /**
     * Retorna o recurso base associado a estes detalhes digitais.
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
