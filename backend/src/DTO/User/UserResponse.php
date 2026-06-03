<?php

namespace App\DTO\User;

final readonly class UserResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {
    }
}