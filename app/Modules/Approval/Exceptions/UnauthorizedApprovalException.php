<?php

namespace App\Modules\Approval\Exceptions;

use Exception;

class UnauthorizedApprovalException extends Exception
{
    public function __construct()
    {
        parent::__construct("You are not authorized to perform this approval action");
    }
}