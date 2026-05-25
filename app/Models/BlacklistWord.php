<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * @property int $id
 * @property string $word
 * @property string|null $category
 * @property string|null $severity
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $createdBy
 */
class BlacklistWord extends Model
{
    protected $fillable = [
        'word',
        'category',
        'severity',
        'created_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
