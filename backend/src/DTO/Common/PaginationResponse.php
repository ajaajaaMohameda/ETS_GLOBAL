<?php

namespace App\DTO\Common;

final readonly class PaginationResponse
{
    public function __construct(
        public int $page,
        public int $limit,
        public int $total,
        public int $pages,
    ) {
    }
}