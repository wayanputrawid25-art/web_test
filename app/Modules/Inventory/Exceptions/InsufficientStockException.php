<?php

namespace App\Modules\Inventory\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(int $productId, int $requested, int $available)
    {
        parent::__construct("Insufficient stock for product ID {$productId}. Requested: {$requested}, Available: {$available}");
    }
}