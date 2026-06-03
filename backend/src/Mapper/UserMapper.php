<?php

namespace App\Mapper;

use App\Document\User;
use App\DTO\User\UserResponse;

final class UserMapper
{
    public function toResponse(User $user): UserResponse
    {
        return new UserResponse(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail(),
        );
    }
}