<?php

namespace App\Modules\Approval\Exceptions;

use Exception;

class ApprovalNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Approval request with ID {$id} not found");
    }
}