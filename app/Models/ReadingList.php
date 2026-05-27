<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingList extends Model
{
    protected $fillable = ['user_id', 'name'];

    public const DEFAULT_LISTS = ['Quero ler', 'A ler', 'Lido'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ReadingListItem::class);
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'reading_list_items')
            ->withTimestamps();
    }
}
