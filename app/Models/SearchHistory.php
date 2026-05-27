<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'query', 'searched_at'];

    protected function casts(): array
    {
        return ['searched_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
