<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingListItem extends Model
{
    protected $fillable = ['reading_list_id', 'resource_id'];

    public function list()
    {
        return $this->belongsTo(ReadingList::class, 'reading_list_id');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
