<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password'])]
#[Hidden(['password'])]
/**
 * Um usuario pode tanto possuir recursos quanto reserva-los, por isso o modelo representa dois papeis no dominio.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
        ];
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
}
