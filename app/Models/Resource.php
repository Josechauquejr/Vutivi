<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Resource e a raiz agregada de todo item emprestavel.
 * Os dados especificos de cada tipo ficam em tabelas irmas para manter as regras comuns em um unico lugar.
 */
class Resource extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'type',
        'status',
        'quantity_available',
        'owner_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Resource $resource) {
            $resource->slug = $resource->slug ?: static::uniqueSlug($resource->title);
        });

        static::updating(function (Resource $resource) {
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

    /**
     * Retorna o usuario dono do recurso.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
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
