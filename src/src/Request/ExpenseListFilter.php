<?php

namespace App\Request;

class ExpenseListFilter
{
    public function __construct(
        public ?string $category = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
    ) {}
}