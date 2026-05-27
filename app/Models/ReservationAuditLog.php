<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationAuditLog extends Model
{
    protected $fillable = [
        'reservation_id',
        'user_id',
        'event',
        'from_status',
        'to_status',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
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
}
