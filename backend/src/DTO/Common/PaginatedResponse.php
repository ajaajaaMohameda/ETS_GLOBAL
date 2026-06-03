<?php

namespace App\DTO\Common;

final readonly class PaginatedResponse
{
    public function __construct(
        public array $data,
        public PaginationResponse $pagination,
    ) {
    }
}