<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Garante que a reserva respeita o tipo do recurso, a janela de uso e a disponibilidade.
 */
class ValidateReservationAgainstResource
{
    /**
     * Executa as validacoes de negocio da reserva em relacao ao recurso.
     *
     * @param array<string, mixed> $reservationData
     */
    public function handle(Resource $resource, array $reservationData, ?Reservation $currentReservation = null): void
    {
        $userId = (int) ($reservationData['user_id'] ?? 0);

        $this->ensureResourceTypeMatches($resource, (string) $reservationData['type']);
        $this->ensureResourceCanBeBorrowedByUser($resource, $userId);
        $this->ensureUserHasNoOverdueLoans($userId, $currentReservation);
        $this->ensureUserHasNotExceededLoanLimit($userId, $currentReservation);
        $this->ensureUserDoesNotHaveOpenReservation($resource, $userId, $currentReservation);
        $this->ensureReservationWindowIsAllowed(
            $resource,
            (string) $reservationData['start_date'],
            (string) $reservationData['end_date'],
        );
        $this->ensureResourceHasAvailability($resource, $currentReservation);
    }

    /**
     * Garante que o tipo informado na reserva corresponde ao tipo real do recurso.
     */
    private function ensureResourceTypeMatches(Resource $resource, string $type): void
    {
        if ($resource->type !== $type) {
            throw ValidationException::withMessages([
                'type' => 'O tipo da reserva não corresponde ao recurso.',
            ]);
        }
    }

    private function ensureResourceCanBeBorrowedByUser(Resource $resource, int $userId): void
    {
        if ((int) $resource->owner_id === $userId) {
            throw ValidationException::withMessages([
                'resource_id' => 'Este recurso pertence a si. Não é possível solicitar empréstimo do próprio recurso.',
            ]);
        }

        if ($resource->type === 'digital') {
            throw ValidationException::withMessages([
                'resource_id' => 'Recursos digitais não entram no fluxo de empréstimos físicos.',
            ]);
        }
    }

    private function ensureUserHasNoOverdueLoans(int $userId, ?Reservation $currentReservation): void
    {
        $query = Reservation::where('user_id', $userId)
            ->whereNull('returned_at')
            ->whereIn('status', [Reservation::STATUS_IN_USE, Reservation::STATUS_EXTENDED])
            ->whereDate('end_date', '<', now()->toDateString());

        if ($currentReservation !== null) {
            $query->whereKeyNot($currentReservation->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'resource_id' => 'Tem empréstimos em atraso. Devolva os recursos em falta antes de solicitar novos empréstimos.',
            ]);
        }
    }

    private function ensureUserHasNotExceededLoanLimit(int $userId, ?Reservation $currentReservation): void
    {
        $query = Reservation::where('user_id', $userId)
            ->whereNull('returned_at')
            ->whereIn('status', Reservation::COPY_HOLDING_STATUSES);

        if ($currentReservation !== null) {
            $query->whereKeyNot($currentReservation->getKey());
        }

        if ($query->count() >= Reservation::MAX_SIMULTANEOUS_LOANS) {
            throw ValidationException::withMessages([
                'resource_id' => 'Atingiu o limite de ' . Reservation::MAX_SIMULTANEOUS_LOANS . ' empréstimos simultâneos.',
            ]);
        }
    }

    private function ensureUserDoesNotHaveOpenReservation(Resource $resource, int $userId, ?Reservation $currentReservation): void
    {
        $query = $resource->reservations()
            ->where('user_id', $userId)
            ->whereNull('returned_at')
            ->whereIn('status', [
                Reservation::STATUS_PENDING,
                ...Reservation::COPY_HOLDING_STATUSES,
            ]);

        if ($currentReservation !== null) {
            $query->whereKeyNot($currentReservation->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'resource_id' => 'Já existe uma reserva ou empréstimo ativo deste recurso para este utilizador.',
            ]);
        }
    }

    /**
     * Garante que a data final nao ultrapasse a politica do subtipo do recurso.
     */
    private function ensureReservationWindowIsAllowed(Resource $resource, string $startDate, string $endDate): void
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($end->gt($this->allowedEndDate($resource, $start))) {
            throw ValidationException::withMessages([
                'end_date' => 'A data final ultrapassa o limite permitido para este recurso.',
            ]);
        }
    }

    /**
     * Retorna a ultima data permitida conforme a politica do subtipo concreto.
     */
    private function allowedEndDate(Resource $resource, Carbon $startDate): Carbon
    {
        if ($resource->type === 'physical') {
            return $startDate->copy()->addDays($resource->physicalResource->max_loan_days);
        }

        return $startDate->copy()->addDays($resource->digitalResource->access_days);
    }

    /**
     * Garante que ainda exista vaga disponivel para a reserva.
     */
    private function ensureResourceHasAvailability(Resource $resource, ?Reservation $currentReservation): void
    {
        if ($resource->type !== 'physical') {
            return;
        }

        $availableCopies = (int) $resource->quantity_available;

        if (
            $currentReservation !== null
            && (int) $currentReservation->resource_id === (int) $resource->id
            && in_array($currentReservation->status, Reservation::COPY_HOLDING_STATUSES, true)
            && $currentReservation->returned_at === null
        ) {
            $availableCopies++;
        }

        if ($availableCopies <= 0) {
            throw ValidationException::withMessages([
                'resource_id' => 'Sem cópias disponíveis.',
            ]);
        }
    }
}
