<?php

namespace App\Exception;

final class ReservationAlreadyExistsException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'User already reserved this session.'
        );
    }
}