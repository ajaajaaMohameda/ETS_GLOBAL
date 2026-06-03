<?php

namespace App\Exception;

use RuntimeException;

final class EmailAlreadyUsedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Email is already used.');
    }
}