<?php

namespace Tests\Feature\Users;

use App\Models\PhysicalResource;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fixa o contrato HTTP do usuario para que mudancas internas continuem seguras.
 */
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
        $this->post(route('users.store'), [
            'name' => 'Maria Silva',
            'username' => 'maria.silva',
            'email' => 'maria@example.com',
            'password' => 'Strong@123',
            'password_confirmation' => 'Strong@123',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Usuario criado com sucesso. Faca login para continuar.');

        $this->assertDatabaseHas('users', [
            'username' => 'maria.silva',
            'email' => 'maria@example.com',
        ]);
    }

    public function test_current_user_is_logged_out_after_deleting_their_account(): void
    {
        $user = User::factory()->create([
            'username' => 'self.delete',
            'email' => 'self.delete@example.com',
        ]);

        $this->actingAs($user);

        $this->from(route('users.edit'))
            ->delete(route('users.destroy'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Sua conta foi excluida com sucesso.');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_user_cannot_be_deleted_while_it_still_owns_resources(): void
    {
        $user = User::factory()->create([
            'username' => 'resource.owner',
            'email' => 'resource.owner@example.com',
        ]);

        $resource = Resource::create([
            'title' => 'Livro Vinculado',
            'description' => 'Recurso associado ao usuario',
            'type' => 'physical',
            'status' => 'available',
            'quantity_available' => 1,
            'owner_id' => $user->id,
        ]);

        PhysicalResource::create([
            'resource_id' => $resource->id,
            'location' => 'Armario 7',
            'max_loan_days' => 5,
            'condition' => 'good',
        ]);

        $this->actingAs($user);

        $this->from(route('users.edit'))
            ->delete(route('users.destroy'))
            ->assertRedirect(route('users.edit'))
            ->assertSessionHas('error', 'Nao e possivel excluir o usuario, pois ele possui recursos associados.');

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
