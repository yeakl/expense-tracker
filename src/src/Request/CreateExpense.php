<?php

namespace App\Request;

use App\Enum\Currency;

readonly class CreateExpense
{
    public function __construct(
        public int $amount,
        public Currency $currency,
        public string $description = '',
        public ?string $category = null,
        public \DateTimeImmutable $date = new \DateTimeImmutable(),
    ) {}
}
