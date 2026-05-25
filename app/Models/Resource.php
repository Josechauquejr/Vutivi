<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Resource e a raiz agregada de todo item emprestavel.
 * Os dados especificos de cada tipo ficam em tabelas irmas para manter as regras comuns em um unico lugar.
 *
 * @property int $id
 * @property string $title
 * @property string|null $authors
 * @property string|null $description
 * @property string $type
 * @property string $status
 * @property string|null $moderation_status
 * @property int|null $moderation_score
 * @property string|null $moderation_reason
 * @property int|null $moderated_by
 * @property bool|null $moderation_auto
 * @property int $quantity_available
 * @property int|null $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $owner
 * @property-read User|null $moderator
 * @property-read DigitalResource|null $digitalResource
 * @property-read PhysicalResource|null $physicalResource
 */
class Resource extends Model
{
    protected $fillable = [
        'title',
        'title_normalized',
        'authors',
        'slug',
        'description',
        'cover_image',
        'type',
        'status',
        'moderation_status',
        'moderation_score',
        'moderation_reason',
        'moderated_by',
        'moderated_at',
        'moderation_auto',
        'quantity_available',
        'downloads_count',
        'owner_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Resource $resource) {
            $resource->title_normalized = static::normalizeTitle((string) $resource->title);
            $resource->slug = $resource->slug ?: static::uniqueSlug($resource->title);
        });

        static::updating(function (Resource $resource) {
            if ($resource->isDirty('title')) {
                $resource->title_normalized = static::normalizeTitle((string) $resource->title);
            }

            if ($resource->isDirty('title') && blank($resource->slug)) {
                $resource->slug = static::uniqueSlug($resource->title, $resource->id);
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'recurso';
        $slug = $base;
        $index = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$index}";
            $index++;
        }

        return $slug;
    }

    public static function normalizeTitle(string $title): string
    {
        return Str::lower(Str::squish($title));
    }

    /**
     * Retorna o usuario dono do recurso.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Retorna os detalhes fisicos quando o recurso pertence a esse subtipo.
     */
    public function physicalResource()
    {
        return $this->hasOne(PhysicalResource::class);
    }

    /**
     * Retorna os detalhes digitais quando o recurso pertence a esse subtipo.
     */
    public function digitalResource()
    {
        return $this->hasOne(DigitalResource::class);
    }

    /**
     * Retorna o historico de reservas ligadas a este recurso.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Retorna os termos e condições deste recurso.
     */
    public function terms()
    {
        return $this->hasMany(TermAndCondition::class);
    }

    /**
     * Retorna usuarios que marcaram este recurso como favorito.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'resource_user_favorites')->withTimestamps();
    }
}
