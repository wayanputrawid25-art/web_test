<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    public function proxies(): array
    {
        return [
            Request::HEADER_X_FORWARDED_FOR,
            Request::HEADER_X_FORWARDED_HOST,
            Request::HEADER_X_FORWARDED_PORT,
            Request::HEADER_X_FORWARDED_PROTO,
            Request::HEADER_X_FORWARDED_AWS_ELB,
        ];
    }

    public function headers(): int
    {
        return Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
            | Request::HEADER_X_FORWARDED_AWS_ELB;
    }
}