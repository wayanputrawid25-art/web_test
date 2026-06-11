<?php

namespace App\Modules\Users\Exceptions;

use Exception;

class DuplicateEmailException extends Exception
{
    public function __construct(string $email)
    {
        parent::__construct("Email {$email} is already registered");
    }
}