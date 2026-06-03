<?php

namespace App\DTO\Session;

final readonly class SessionResponse
{
    public function __construct(
        public string $id,
        public string $language,
        public string $startsAt,
        public string $location,
        public int $capacity,
        public int $availableSeats,
    ) {
    }
}