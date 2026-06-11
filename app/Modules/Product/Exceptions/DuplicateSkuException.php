<?php

namespace App\Modules\Product\Exceptions;

use Exception;

class DuplicateSkuException extends Exception
{
    public function __construct(string $sku)
    {
        parent::__construct("SKU {$sku} sudah digunakan");
    }
}