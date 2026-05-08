<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Protege o contrato de login para que refactors internos nao alterem o comportamento HTTP percebido pelo usuario.
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function teste_de_login_e_logout(): void
    {
        $user = User::factory()->create([
            'username' => 'test.user',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'username' => 'test.user',
            'password' => 'password',
        ])->assertRedirect('/home');

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_login_shows_a_clear_error_when_credentials_are_invalid(): void
    {
        User::factory()->create([
            'username' => 'valid.user',
            'email' => 'valid@example.com',
            'password' => 'password',
        ]);

        $this->from('/login')
            ->post('/login', [
                'username' => 'valid.user',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'Credenciais invalidas.');

        $this->assertGuest();
    }
}
