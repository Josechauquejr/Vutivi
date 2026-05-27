<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceWaitlist extends Model
{
    protected $table = 'resource_waitlist';

    protected $fillable = [
        'resource_id',
        'user_id',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'notified_at' => 'datetime',
        ];
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
