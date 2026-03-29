<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role === User::ROLE_ADMIN) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
