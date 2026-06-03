<?php

namespace App\DTO\Profile;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateProfileRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name is required.')]
        #[Assert\Length(min: 2, max: 100)]
        public string $name,

        #[Assert\NotBlank(message: 'Email is required.')]
        #[Assert\Email(message: 'Email is invalid.')]
        public string $email,
    ) {
    }
}