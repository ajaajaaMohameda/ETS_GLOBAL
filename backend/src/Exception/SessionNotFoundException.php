<?php

namespace App\Exception;

use RuntimeException;

final class SessionNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Session not found.');
    }
}