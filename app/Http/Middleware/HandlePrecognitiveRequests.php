<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests as Middleware;

class HandlePrecognitiveRequests extends Middleware
{
    public function except(): array
    {
        return [
            //
        ];
    }
}