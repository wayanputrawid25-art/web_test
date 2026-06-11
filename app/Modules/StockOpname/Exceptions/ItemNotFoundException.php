<?php

namespace App\Modules\StockOpname\Exceptions;

use Exception;

class ItemNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Stock Opname Item with ID {$id} not found");
    }
}