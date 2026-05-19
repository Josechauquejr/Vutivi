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
        'status',
        'approved_by',
        'approved_at',
        'extension_count',
        'days_overdue',
        'picked_up_at',
        'actual_return_date',
        'extension_reason',
    ];

    // Estados da requisição
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_IN_USE = 'in_use';
    const STATUS_EXTENSION_PENDING = 'extension_pending';
    const STATUS_EXTENDED = 'extended';
    const STATUS_RETURNED = 'returned';
    const STATUS_CANCELLED = 'cancelled';

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
            'picked_up_at' => 'datetime',
            'approved_at' => 'datetime',
            'actual_return_date' => 'date',
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

    /**
     * Retorna o admin que aprovou (se houver).
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Retorna as aceitações de termos desta reserva.
     */
    public function termAcceptances()
    {
        return $this->hasMany(TermAcceptance::class);
    }

    /**
     * Verifica se a reserva pode ser estendida.
     */
    public function canExtend(): bool
    {
        return $this->status === self::STATUS_IN_USE &&
               $this->extension_count < $this->resource->physicalResource->max_extensions &&
               $this->resource->physicalResource->allow_extension;
    }

    /**
     * Verifica se a reserva está atrasada.
     */
    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_IN_USE &&
               now()->toDateString() > $this->end_date->toDateString();
    }

    /**
     * Retorna quantos dias está atrasado.
     */
    public function daysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return now()->diffInDays($this->end_date);
    }

    /**
     * Scope: Reservas pendentes de aprovação.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Reservas ativas (em uso).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_APPROVED,
            self::STATUS_IN_USE,
            self::STATUS_EXTENDED
        ]);
    }

    /**
     * Scope: Reservas que venceram.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_IN_USE)
            ->whereRaw('end_date < CURDATE()');
    }
}
