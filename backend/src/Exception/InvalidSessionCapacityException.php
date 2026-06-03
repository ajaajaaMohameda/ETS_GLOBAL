<?php

namespace App\Exception;

use RuntimeException;

final class InvalidSessionCapacityException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'Capacity cannot be lower than already reserved seats.'
        );
    }
}