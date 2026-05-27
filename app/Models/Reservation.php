<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Reservas preservam o historico de emprestimo; a devolucao encerra o registro sem apaga-lo.
 */
class Reservation extends Model
{
    use SoftDeletes;

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
        'extension_requested_at',
        'extension_decision',
        'extension_decided_at',
        'extension_decision_note',
        'extension_reviewed_by',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_IN_USE = 'in_use';
    const STATUS_EXTENSION_PENDING = 'extension_pending';
    const STATUS_EXTENDED = 'extended';
    const STATUS_RETURNED = 'returned';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DENIED = 'denied';

    public const EXTENSION_APPROVED = 'approved';
    public const EXTENSION_DENIED = 'denied';

    public const COPY_HOLDING_STATUSES = [
        self::STATUS_APPROVED,
        self::STATUS_IN_USE,
        self::STATUS_EXTENSION_PENDING,
        self::STATUS_EXTENDED,
    ];

    public const ACTIVE_LOAN_STATUSES = [
        self::STATUS_IN_USE,
        self::STATUS_EXTENSION_PENDING,
        self::STATUS_EXTENDED,
    ];

    public const MAX_SIMULTANEOUS_LOANS = 3;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'picked_up_at' => 'datetime',
            'approved_at' => 'datetime',
            'actual_return_date' => 'date',
            'returned_at' => 'datetime',
            'extension_requested_at' => 'datetime',
            'extension_decided_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function extensionReviewer()
    {
        return $this->belongsTo(User::class, 'extension_reviewed_by');
    }

    public function termAcceptances()
    {
        return $this->hasMany(TermAcceptance::class);
    }

    public function canExtend(): bool
    {
        // Garantir que a relação está carregada antes de aceder
        $physicalResource = $this->relationLoaded('resource')
            ? $this->resource?->physicalResource
            : $this->load('resource.physicalResource')->resource?->physicalResource;

        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_IN_USE, self::STATUS_EXTENDED], true)
            && $physicalResource !== null
            && (int) $this->extension_count < (int) $physicalResource->max_extensions
            && $physicalResource->allow_extension;
    }

    public function isOverdue(): bool
    {
        return in_array($this->status, [self::STATUS_IN_USE, self::STATUS_EXTENDED], true)
            && now()->toDateString() > $this->end_date->toDateString();
    }

    public function daysOverdue(): int
    {
        if (! $this->isOverdue()) {
            return 0;
        }

        // diffInDays com $absolute=true devolve sempre positivo
        return (int) $this->end_date->diffInDays(now());
    }

    /** Scope: pendentes de aprovação. */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /** Scope: activos (em posse). */
    public function scopeActive($query)
    {
        return $query->whereIn('status', self::COPY_HOLDING_STATUSES);
    }

    /** Scope: fora do prazo — inclui in_use E extended. */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', [self::STATUS_IN_USE, self::STATUS_EXTENDED])
            ->whereDate('end_date', '<', now()->toDateString());
    }
}
