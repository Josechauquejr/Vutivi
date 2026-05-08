<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Popula a base de dados da aplicacao.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Usuario de Teste',
            'username' => 'usuario.teste',
            'email' => 'teste@examplo.com',
            'password' => 'password',
        ]);

        $this->call(ResourceSeeder::class);
    }
}
