<?php

namespace Tests\Feature\Resources;

use App\Models\DigitalResource;
use App\Models\PhysicalResource;
use App\Models\Resource;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Protege a divisao atual do schema para que simplificacoes bem intencionadas nao derrubem relacoes distintas.
 */
class ResourceSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_current_resource_tables_and_relationships(): void
    {
        $owner = User::factory()->create([
            'username' => 'owner.user',
            'email' => 'owner@example.com',
        ]);

        $borrower = User::factory()->create([
            'username' => 'borrower.user',
            'email' => 'borrower@example.com',
        ]);

        $physicalResource = Resource::create([
            'title' => 'Livro de Redes',
            'description' => 'Exemplar fisico',
            'type' => 'physical',
            'status' => 'available',
            'quantity_available' => 2,
            'owner_id' => $owner->id,
        ]);

        $digitalResource = Resource::create([
            'title' => 'Manual Digital',
            'description' => 'Arquivo PDF',
            'type' => 'digital',
            'status' => 'active',
            'quantity_available' => 1,
            'owner_id' => $owner->id,
        ]);

        $physicalDetails = PhysicalResource::create([
            'resource_id' => $physicalResource->id,
            'location' => 'Estante A',
            'max_loan_days' => 7,
            'condition' => 'good',
        ]);

        $digitalDetails = DigitalResource::create([
            'resource_id' => $digitalResource->id,
            'file_path' => 'resources/manual-digital.pdf',
            'access_type' => 'view',
            'access_days' => 30,
        ]);

        $reservation = Reservation::create([
            'resource_id' => $physicalResource->id,
            'user_id' => $borrower->id,
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
        ]);

        $this->assertDatabaseHas('resources', [
            'id' => $physicalResource->id,
            'title' => 'Livro de Redes',
            'quantity_available' => 2,
            'owner_id' => $owner->id,
        ]);

        $this->assertDatabaseHas('physical_resources', [
            'resource_id' => $physicalResource->id,
            'location' => 'Estante A',
        ]);

        $this->assertDatabaseHas('digital_resources', [
            'resource_id' => $digitalResource->id,
            'access_type' => 'view',
        ]);

        $this->assertDatabaseHas('reservations', [
            'resource_id' => $physicalResource->id,
            'user_id' => $borrower->id,
            'type' => 'physical',
        ]);

        $this->assertTrue($physicalResource->owner->is($owner));
        $this->assertTrue($physicalResource->physicalResource->is($physicalDetails));
        $this->assertTrue($digitalResource->digitalResource->is($digitalDetails));
        $this->assertTrue($reservation->resource->is($physicalResource));
        $this->assertTrue($reservation->user->is($borrower));
    }
}
