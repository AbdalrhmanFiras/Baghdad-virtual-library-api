<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Middleware\ValidatePathEncoding as BaseMiddleware;
use Illuminate\Http\Request;

class SkipValidatePathEncodingForFilament extends BaseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // تخطي كل طلبات Filament
        if ($request->is('_filament/*')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
