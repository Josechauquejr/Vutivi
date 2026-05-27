<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceReview extends Model
{
    protected $fillable = ['resource_id', 'user_id', 'stars', 'body'];

    protected function casts(): array
    {
        return ['stars' => 'integer'];
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
