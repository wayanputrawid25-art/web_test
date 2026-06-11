<?php

namespace App\Modules\Users\Exceptions;

use Exception;

class InvalidRoleException extends Exception
{
    public function __construct(string $role)
    {
        parent::__construct("Invalid role: {$role}");
    }
}