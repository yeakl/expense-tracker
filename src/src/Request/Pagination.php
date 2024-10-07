<?php

namespace App\Request;

readonly class Pagination
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 10,
    ) {}
}