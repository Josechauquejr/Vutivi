<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'username', 'slug', 'email', 'profile_photo', 'password', 'role', 'banned_at', 'ban_reason'])]
#[Hidden(['password'])]
/**
 * Um usuario pode tanto possuir recursos quanto reserva-los, por isso o modelo representa dois papeis no dominio.
 *
 * @property int $id
 * @property string $name
 * @property string|null $username
 * @property string $email
 * @property string|null $profile_photo
 * @property string|null $role
 * @property \Illuminate\Support\Carbon|null $banned_at
 * @property string|null $ban_reason
 * @property int|null $resources_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->slug = $user->slug ?: static::uniqueSlug($user->username ?: $user->name);
        });

        static::updating(function (User $user) {
            if (($user->isDirty('username') || $user->isDirty('name')) && blank($user->slug)) {
                $user->slug = static::uniqueSlug($user->username ?: $user->name, $user->id);
            }
        });
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'utilizador';
        $slug = $base;
        $index = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$index}";
            $index++;
        }

        return $slug;
    }

    /**
     * Retorna os casts aplicados aos atributos do modelo.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // O hash no limite do modelo protege qualquer caminho de escrita, nao apenas formularios.
            'password' => 'hashed',
            'banned_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin'], true);
    }

    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    /**
     * Retorna os recursos cadastrados sob responsabilidade deste usuario.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class, 'owner_id');
    }

    /**
     * Retorna as reservas feitas por este usuario.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Retorna recursos marcados como favoritos pelo usuario.
     */
    public function favoriteResources()
    {
        return $this->belongsToMany(Resource::class, 'resource_user_favorites')->withTimestamps();
    }

    public function moderationNotifications()
    {
        return $this->hasMany(ModerationNotification::class);
    }
}
