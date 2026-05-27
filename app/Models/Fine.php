<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $fillable = [
        'reservation_id',
        'user_id',
        'days_overdue',
        'rate_per_day',
        'total',
        'paid_at',
    ];

    public const RATE_PER_DAY = 0.50;

    protected function casts(): array
    {
        return [
            'rate_per_day' => 'decimal:2',
            'total'        => 'decimal:2',
            'paid_at'      => 'datetime',
        ];
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }
}
