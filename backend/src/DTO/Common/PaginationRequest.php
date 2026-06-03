<?php

namespace App\DTO\Common;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class PaginationRequest
{
    public function __construct(
        #[Assert\Positive]
        public int $page = 1,

        #[Assert\Range(
            min: 1,
            max: 100
        )]
        public int $limit = 10,
    ) {
    }

    public function getSkip(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}