<?php

namespace App\Modules\StockOpname\Exceptions;

use Exception;

class InvalidStockOpnameTransitionException extends Exception
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition from {$from} to {$to}");
    }
}