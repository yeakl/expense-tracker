<?php

namespace App\Request;

use App\Enum\Currency;
use Symfony\Component\Validator\Constraints as Assert;

class TotalExpenseFilter
{
    public ?Currency $currency = null;
    public ?int $year  = null;
    public ?int $month = null;
}