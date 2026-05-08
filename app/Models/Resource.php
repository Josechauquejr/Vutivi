<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Resource e a raiz agregada de todo item emprestavel.
 * Os dados especificos de cada tipo ficam em tabelas irmas para manter as regras comuns em um unico lugar.
 */
class Resource extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'quantity_available',
        'owner_id',
    ];

    /**
     * Retorna o usuario dono do recurso.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Retorna os detalhes fisicos quando o recurso pertence a esse subtipo.
     */
    public function physicalResource()
    {
        return $this->hasOne(PhysicalResource::class);
    }

    /**
     * Retorna os detalhes digitais quando o recurso pertence a esse subtipo.
     */
    public function digitalResource()
    {
        return $this->hasOne(DigitalResource::class);
    }

    /**
     * Retorna o historico de reservas ligadas a este recurso.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
