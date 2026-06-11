<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests
{
    public function handle(Request $request, \Closure $next): Response
    {
        return $next($request);
    }

    public function share(Request $request): array
    {
        return [
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
            ],
        ];
    }
}