<?php

namespace Tests\Feature\Resources;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Protege o contrato de CRUD do recurso digital enquanto a persistencia do subtipo evolui.
 */
class DigitalResourceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_digital_resource_crud_endpoints_work_via_http_requests(): void
    {
        $owner = User::factory()->create([
            'username' => 'owner.digital',
            'email' => 'owner.digital@example.com',
        ]);

        $this->actingAs($owner);

        $this->get(route('digital-resources.index'))->assertOk();
        $this->get(route('digital-resources.create'))->assertOk();

        $payload = [
            'title' => 'Manual Laravel',
            'description' => 'Documento digital',
            'status' => 'available',
            'quantity_available' => 3,
            'file_path' => 'docs/manual-laravel.pdf',
            'access_type' => 'view',
            'access_days' => 30,
        ];

        $this->post(route('digital-resources.store'), $payload)
            ->assertRedirect();

        $resource = Resource::where('title', 'Manual Laravel')->firstOrFail();

        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'type' => 'digital',
            'owner_id' => $owner->id,
        ]);

        $this->assertDatabaseHas('digital_resources', [
            'resource_id' => $resource->id,
            'file_path' => 'docs/manual-laravel.pdf',
            'access_type' => 'view',
        ]);

        $this->get(route('digital-resources.show', $resource->id))
            ->assertOk()
            ->assertSee('Manual Laravel');

        $this->get(route('digital-resources.edit', $resource->id))->assertOk();

        $this->put(route('digital-resources.update', $resource->id), [
            'title' => 'Manual Laravel 2',
            'description' => 'Versao revista',
            'status' => 'active',
            'quantity_available' => 5,
            'file_path' => 'docs/manual-laravel-v2.pdf',
            'access_type' => 'download',
            'access_days' => 60,
        ])->assertRedirect(route('digital-resources.show', $resource->id));

        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'title' => 'Manual Laravel 2',
            'status' => 'active',
            'quantity_available' => 5,
        ]);

        $this->assertDatabaseHas('digital_resources', [
            'resource_id' => $resource->id,
            'file_path' => 'docs/manual-laravel-v2.pdf',
            'access_type' => 'download',
            'access_days' => 60,
        ]);

        $this->delete(route('digital-resources.destroy', $resource->id))
            ->assertRedirect(route('digital-resources.index'));

        $this->assertDatabaseMissing('resources', [
            'id' => $resource->id,
        ]);
    }
}
