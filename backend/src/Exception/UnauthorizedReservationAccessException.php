<?php

namespace App\Exception;

final class UnauthorizedReservationAccessException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'Access denied.'
        );
    }
}