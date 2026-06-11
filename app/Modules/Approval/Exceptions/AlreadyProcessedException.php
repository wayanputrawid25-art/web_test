<?php

namespace App\Modules\Approval\Exceptions;

use Exception;

class AlreadyProcessedException extends Exception
{
    public function __construct()
    {
        parent::__construct("This approval request has already been processed");
    }
}