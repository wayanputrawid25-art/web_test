<?php

namespace App\Modules\StockOpname\Exceptions;

use Exception;

class NegativeQuantityException extends Exception
{
    public function __construct()
    {
        parent::__construct("Quantity cannot be negative");
    }
}