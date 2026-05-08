<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * PhysicalResource guarda apenas os detalhes que fazem sentido para itens fisicos.
 */
class PhysicalResource extends Model
{
    protected $fillable = [
        'resource_id',
        'location',
        'max_loan_days',
        'condition',
    ];

    // O schema atual trata essas linhas como detalhes estaticos, entao timestamps nao agregariam valor.
    public $timestamps = false;

    /**
     * Retorna o recurso base associado a estes detalhes fisicos.
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
