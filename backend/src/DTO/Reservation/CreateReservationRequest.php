<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateReservationRequest
{
    #[Assert\NotBlank(message: 'The session ID is required.')]
    #[Assert\Type(type: 'string')]
    public string $sessionId;
}