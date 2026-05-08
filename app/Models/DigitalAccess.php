<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TODO: Revisar este modelo quando o fluxo de acesso digital estiver modelado de ponta a ponta.
 * O formato atual ainda parece mais um esqueleto inicial do que um objeto de dominio consolidado.
 */
class DigitalAccess extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'quantity_available',
        'owner_id',
    ];

    /**
     * Retorna o recurso digital associado a este registro de acesso.
     */
    public function digitalResource()
    {
        return $this->belongsTo(DigitalResource::class);
    }

    /**
     * Retorna o usuario relacionado a este acesso digital.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
