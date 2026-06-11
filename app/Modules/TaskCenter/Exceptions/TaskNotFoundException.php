<?php

namespace App\Modules\TaskCenter\Exceptions;

use Exception;

class TaskNotFoundException extends Exception
{
    public function __construct(int $taskId)
    {
        parent::__construct("Task with ID {$taskId} not found");
    }
}