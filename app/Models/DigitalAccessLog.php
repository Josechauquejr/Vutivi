<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalAccessLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['resource_id', 'user_id', 'action', 'ip', 'accessed_at'];

    public const ACTION_VIEW     = 'view';
    public const ACTION_DOWNLOAD = 'download';

    protected function casts(): array
    {
        return ['accessed_at' => 'datetime'];
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
