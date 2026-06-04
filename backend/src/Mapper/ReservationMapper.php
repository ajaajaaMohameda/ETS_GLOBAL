<?php

namespace App\Mapper;

use App\Document\Reservation;
use App\Dto\ReservationResponse;

final readonly class ReservationMapper
{
    /**
     * @param Reservation[] $reservations
     * @return ReservationResponse[]
     */
    public function mapToResponseList(array $reservations): array
    {
        return array_map(
            fn (Reservation $reservation) => $this->mapToResponse($reservation),
            $reservations
        );
    }

    public function mapToResponse(Reservation $reservation): ReservationResponse
    {
        $session = $reservation->getSession();

        return new ReservationResponse(
            id: $reservation->getId(),
            sessionId: $session->getId(),
            language: $session->getLanguage(),
            date: $session->getDate()->format('Y-m-d'),
            time: $session->getTime(),
            location: $session->getLocation(),
            reservedAt: $reservation->getReservedAt()->format(\DateTimeInterface::ATOM),
            isCancelled: $reservation->isCancelled()
        );
    }
}