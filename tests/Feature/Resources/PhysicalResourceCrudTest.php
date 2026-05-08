<?php

namespace Tests\Feature\Resources;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Protege o contrato de CRUD do recurso fisico enquanto a implementacao do subtipo muda com o tempo.
 */
class PhysicalResourceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_physical_resource_crud_endpoints_work_via_http_requests(): void
    {
        $owner = User::factory()->create([
            'username' => 'owner.physical',
            'email' => 'owner.physical@example.com',
        ]);

        $this->actingAs($owner);

        $this->get(route('physical-resources.index'))->assertOk();
        $this->get(route('physical-resources.create'))->assertOk();

        $payload = [
            'title' => 'Atlas de Historia',
            'description' => 'Colecao fisica',
            'status' => 'available',
            'quantity_available' => 2,
            'location' => 'Estante Central',
            'max_loan_days' => 10,
            'condition' => 'good',
        ];

        $this->post(route('physical-resources.store'), $payload)
            ->assertRedirect();

        $resource = Resource::where('title', 'Atlas de Historia')->firstOrFail();

        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'type' => 'physical',
            'owner_id' => $owner->id,
        ]);

        $this->assertDatabaseHas('physical_resources', [
            'resource_id' => $resource->id,
            'location' => 'Estante Central',
        ]);

        $this->get(route('physical-resources.show', $resource->id))
            ->assertOk()
            ->assertSee('Atlas de Historia');

        $this->get(route('physical-resources.edit', $resource->id))->assertOk();

        $this->put(route('physical-resources.update', $resource->id), [
            'title' => 'Atlas Atualizado',
            'description' => 'Colecao revista',
            'status' => 'reserved',
            'quantity_available' => 1,
            'location' => 'Estante Premium',
            'max_loan_days' => 14,
            'condition' => 'excellent',
        ])->assertRedirect(route('physical-resources.show', $resource->id));

        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'title' => 'Atlas Atualizado',
            'status' => 'reserved',
            'quantity_available' => 1,
        ]);

        $this->assertDatabaseHas('physical_resources', [
            'resource_id' => $resource->id,
            'location' => 'Estante Premium',
            'max_loan_days' => 14,
            'condition' => 'excellent',
        ]);

        $this->delete(route('physical-resources.destroy', $resource->id))
            ->assertRedirect(route('physical-resources.index'));

        $this->assertDatabaseMissing('resources', [
            'id' => $resource->id,
        ]);
    }
}
