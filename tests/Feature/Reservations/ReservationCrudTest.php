<?php

namespace Tests\Feature\Reservations;

use App\Models\PhysicalResource;
use App\Models\Resource;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cobre o ciclo de vida da reserva porque regras de disponibilidade quebram facil em refactors inocentes.
 */
class ReservationCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_crud_endpoints_work_via_http_requests(): void
    {
        [$owner, $borrower, $resource] = $this->createPhysicalResourceFixture();

        $this->actingAs($owner);

        $this->get(route('reservations.index'))->assertOk();
        $this->get(route('reservations.create'))->assertOk();

        $payload = [
            'resource_id' => $resource->id,
            'user_id' => $borrower->id,
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
        ];

        $this->post(route('reservations.store'), $payload)
            ->assertRedirect();

        $reservation = Reservation::firstOrFail();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'resource_id' => $resource->id,
            'user_id' => $borrower->id,
            'type' => 'physical',
        ]);

        $this->assertSame('reserved', $resource->fresh()->status);

        $this->get(route('reservations.show', $reservation->id))->assertOk();
        $this->get(route('reservations.edit', $reservation->id))->assertOk();

        $this->put(route('reservations.update', $reservation->id), [
            'resource_id' => $resource->id,
            'user_id' => $borrower->id,
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
        ])->assertRedirect(route('reservations.show', $reservation->id));

        $this->assertSame(
            now()->addDays(7)->toDateString(),
            $reservation->fresh()->end_date->format('Y-m-d')
        );

        $this->patch(route('reservations.return', $reservation->id))
            ->assertRedirect(route('reservations.show', $reservation->id));

        $this->assertNotNull($reservation->fresh()->returned_at);
        $this->assertSame('available', $resource->fresh()->status);

        $this->delete(route('reservations.destroy', $reservation->id))
            ->assertRedirect(route('reservations.index'));

        $this->assertDatabaseMissing('reservations', [
            'id' => $reservation->id,
        ]);
    }

    public function test_reservation_rejects_type_mismatch(): void
    {
        $owner = User::factory()->create([
            'username' => 'owner.mismatch',
            'email' => 'owner.mismatch@example.com',
        ]);

        $borrower = User::factory()->create([
            'username' => 'borrower.mismatch',
            'email' => 'borrower.mismatch@example.com',
        ]);

        $resource = Resource::create([
            'title' => 'Guia Digital',
            'description' => 'Recurso digital',
            'type' => 'digital',
            'status' => 'available',
            'quantity_available' => 1,
            'owner_id' => $owner->id,
        ]);

        $resource->digitalResource()->create([
            'file_path' => 'docs/guia.pdf',
            'access_type' => 'view',
            'access_days' => 15,
        ]);

        $this->actingAs($owner);

        $this->post(route('reservations.store'), [
            'resource_id' => $resource->id,
            'user_id' => $borrower->id,
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
        ])->assertSessionHasErrors('type');
    }

    public function test_reservation_rejects_when_resource_has_no_availability(): void
    {
        [$owner, $borrower, $resource] = $this->createPhysicalResourceFixture();

        Reservation::create([
            'resource_id' => $resource->id,
            'user_id' => $borrower->id,
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
        ]);

        $this->actingAs($owner);

        $this->post(route('reservations.store'), [
            'resource_id' => $resource->id,
            'user_id' => $borrower->id,
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
        ])->assertSessionHasErrors('resource_id');
    }

    public function test_updating_the_reserved_resource_recalculates_old_and_new_availability(): void
    {
        [$owner, $borrower, $firstResource] = $this->createPhysicalResourceFixture();
        $secondResource = $this->createPhysicalResource($owner, 'Livro Reserva 2', 'Armario 2');

        $reservation = Reservation::create([
            'resource_id' => $firstResource->id,
            'user_id' => $borrower->id,
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
        ]);

        $firstResource->update(['status' => 'reserved']);

        $this->actingAs($owner);

        $this->put(route('reservations.update', $reservation->id), [
            'resource_id' => $secondResource->id,
            'user_id' => $borrower->id,
            'type' => 'physical',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
        ])->assertRedirect(route('reservations.show', $reservation->id));

        $this->assertSame('available', $firstResource->fresh()->status);
        $this->assertSame('reserved', $secondResource->fresh()->status);
        $this->assertTrue($reservation->fresh()->resource->is($secondResource));
    }

    private function createPhysicalResourceFixture(): array
    {
        // Esta funcao auxiliar mantem as assercoes focadas no comportamento de negocio, e nao no ruido de preparacao.
        $owner = User::factory()->create([
            'username' => 'owner.reservation',
            'email' => 'owner.reservation@example.com',
        ]);

        $borrower = User::factory()->create([
            'username' => 'borrower.reservation',
            'email' => 'borrower.reservation@example.com',
        ]);

        $resource = $this->createPhysicalResource($owner, 'Livro de Emprestimo', 'Armario 1');

        return [$owner, $borrower, $resource];
    }

    private function createPhysicalResource(User $owner, string $title, string $location): Resource
    {
        $resource = Resource::create([
            'title' => $title,
            'description' => 'Exemplar unico',
            'type' => 'physical',
            'status' => 'available',
            'quantity_available' => 1,
            'owner_id' => $owner->id,
        ]);

        PhysicalResource::create([
            'resource_id' => $resource->id,
            'location' => $location,
            'max_loan_days' => 7,
            'condition' => 'good',
        ]);

        return $resource;
    }
}
