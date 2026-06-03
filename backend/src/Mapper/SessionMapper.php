<?php

namespace App\Mapper;

use App\Document\TestSession;
use App\DTO\Session\SessionResponse;

final class SessionMapper
{
    public function toResponse(
        TestSession $session
    ): SessionResponse {
        return new SessionResponse(
            id: $session->getId(),
            language: $session->getLanguage(),
            startsAt: $session->getStartsAt()->format(DATE_ATOM),
            location: $session->getLocation(),
            capacity: $session->getCapacity(),
            availableSeats: $session->getAvailableSeats(),
        );
    }
}