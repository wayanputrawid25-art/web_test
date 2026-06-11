<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateSignature as Middleware;

class ValidateSignature extends Middleware
{
    protected $except = [
        'fbclid',
        'utm_campaign',
        'utm_content',
        'utm_medium',
        'utm_source',
        'utm_term',
    ];
}