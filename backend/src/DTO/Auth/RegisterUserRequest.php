<?php

namespace App\DTO\Auth;

use Symfony\Component\Validator\Constraints as Assert;

final class RegisterUserRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name is required.')]
        #[Assert\Length(
            min: 2,
            max: 100,
            minMessage: 'Name must contain at least {{ limit }} characters.',
            maxMessage: 'Name cannot exceed {{ limit }} characters.'
        )]
        public readonly string $name,

        #[Assert\NotBlank(message: 'Email is required.')]
        #[Assert\Email(message: 'Email is invalid.')]
        public readonly string $email,

        #[Assert\NotBlank(message: 'Password is required.')]
        #[Assert\Length(
            min: 6,
            minMessage: 'Password must contain at least {{ limit }} characters.'
        )]
        public readonly string $password,
    ) {
    }
}