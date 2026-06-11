<?php

namespace App\Modules\Approval\Exceptions;

use Exception;

class DuplicateApprovalException extends Exception
{
    public function __construct(string $type, int $referenceId)
    {
        parent::__construct("A pending approval request already exists for {$type} with reference ID {$referenceId}");
    }
}