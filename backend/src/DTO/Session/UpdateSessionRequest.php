<?php

namespace App\DTO\Session;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateSessionRequest
{
    public function __construct(
        #[Assert\Length(max: 100)]
        public ?string $language = null,

        #[Assert\DateTime]
        public ?string $startsAt = null,

        #[Assert\Length(max: 255)]
        public ?string $location = null,

        #[Assert\Positive]
        public ?int $capacity = null,
    ) {
    }
}