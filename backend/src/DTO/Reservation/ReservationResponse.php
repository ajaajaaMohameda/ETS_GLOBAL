<?php

namespace App\DTO\Reservation;

final readonly class ReservationResponse
{
    public function __construct(
        public string $id,
        public string $sessionId,
        public string $language,
        public string $location,
        public string $reservedAt,
    ) {
    }
}