<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        // 只允许你这个账号
        if (!$user || $user->email !== 'linmei7918@gmail.com') {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
