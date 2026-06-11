<?php

namespace App\Modules\StockOpname\Exceptions;

use Exception;

class StockOpnameNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Stock Opname Session with ID {$id} not found");
    }
}