<?php
namespace App\Http;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class MiddlewarePipeline
{
    public function handle($request, \Closure $next)
    {
        return pipe($request, [
            EnsureFrontendRequestsAreStateful::class, 
            VerifyCsrfToken::class, 
        ], $next);
    }
}