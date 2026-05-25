<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * DigitalResource separa os metadados de acesso do catalogo compartilhado de recursos.
 *
 * @property int $resource_id
 * @property string|null $file_path
 * @property string|null $file_hash
 * @property string|null $access_type
 * @property int|null $access_days
 * @property-read Resource|null $resource
 */
class DigitalResource extends Model
{
    protected $fillable = [
        'resource_id',
        'file_path',
        'file_hash',
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
