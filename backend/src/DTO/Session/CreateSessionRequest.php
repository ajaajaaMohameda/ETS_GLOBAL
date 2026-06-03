<?php

namespace App\DTO\Session;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateSessionRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public string $language,

        #[Assert\NotBlank]
        #[Assert\DateTime]
        public string $startsAt,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $location,

        #[Assert\Positive]
        public int $capacity,
    ) {
    }
}