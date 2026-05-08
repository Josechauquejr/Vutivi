<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Reservas preservam o historico de emprestimo; a devolucao encerra o registro sem apaga-lo.
 */
class Reservation extends Model
{
    protected $fillable = [
        'resource_id',
        'user_id',
        'type',
        'start_date',
        'end_date',
        'returned_at',
    ];

    /**
     * Retorna os casts aplicados aos atributos do modelo.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            // A disponibilidade depende de a reserva ter sido devolvida ou nao, por isso a precisao de tempo importa.
            'returned_at' => 'datetime',
        ];
    }

    /**
     * Retorna o usuario que realizou a reserva.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retorna o recurso vinculado a reserva.
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
