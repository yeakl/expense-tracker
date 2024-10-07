<?php

namespace App\Request;

readonly class CreateCategory
{
    public function __construct(
        public string $name,
    ) {}
}