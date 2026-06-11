<?php

namespace App\Modules\Product\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Product dengan ID {$id} tidak ditemukan");
    }
}