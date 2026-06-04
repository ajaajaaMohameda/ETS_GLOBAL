<?php

namespace App\Exception;

final class NoAvailableSeatsException
    extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'No available seats.'
        );
    }
}