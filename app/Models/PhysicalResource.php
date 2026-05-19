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
        'requires_approval',
        'max_extensions',
        'allow_extension',
    ];

    // O schema atual trata essas linhas como detalhes estaticos, entao timestamps nao agregariam valor.
    public $timestamps = false;

    protected $casts = [
        'requires_approval' => 'boolean',
        'allow_extension' => 'boolean',
    ];

    /**
     * Retorna o recurso base associado a estes detalhes fisicos.
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * Retorna os termos e condições deste recurso.
     */
    public function terms()
    {
        return $this->resource()->first()?->terms() ?? collect();
    }
}
