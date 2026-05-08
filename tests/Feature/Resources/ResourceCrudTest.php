<?php

namespace Tests\Feature\Resources;

use App\Models\PhysicalResource;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Mantem explicito o comportamento do catalogo publico, especialmente a regra de listar apenas recursos disponiveis.
 */
class ResourceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_index_show_and_destroy_work_via_http_requests(): void
    {
        $owner = User::factory()->create([
            'username' => 'owner.resource',
            'email' => 'owner.resource@example.com',
        ]);

        $this->actingAs($owner);

        $available = Resource::create([
            'title' => 'Livro Disponivel',
            'description' => 'Disponivel para consulta',
            'type' => 'physical',
            'status' => 'available',
            'quantity_available' => 1,
            'owner_id' => $owner->id,
        ]);

        PhysicalResource::create([
            'resource_id' => $available->id,
            'location' => 'Sala A',
            'max_loan_days' => 7,
            'condition' => 'good',
        ]);

        $hidden = Resource::create([
            'title' => 'Livro Reservado',
            'description' => 'Nao deve aparecer na listagem publica',
            'type' => 'physical',
            'status' => 'reserved',
            'quantity_available' => 1,
            'owner_id' => $owner->id,
        ]);

        PhysicalResource::create([
            'resource_id' => $hidden->id,
            'location' => 'Sala B',
            'max_loan_days' => 5,
            'condition' => 'fair',
        ]);

        $this->get(route('resources.index'))
            ->assertOk()
            ->assertSee('Livro Disponivel')
            ->assertDontSee('Livro Reservado');

        $this->get(route('resources.show', $available->id))
            ->assertOk()
            ->assertSee('Livro Disponivel')
            ->assertSee('Sala A');

        $this->delete(route('resources.destroy', $available->id))
            ->assertRedirect(route('resources.index'));

        $this->assertDatabaseMissing('resources', [
            'id' => $available->id,
        ]);

        $this->assertDatabaseMissing('physical_resources', [
            'resource_id' => $available->id,
        ]);
    }
}
