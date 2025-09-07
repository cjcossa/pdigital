<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiry
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->user()?->currentAccessToken();

        if ($token && $token->expires_at && $token->expires_at->isPast()) {
            $token->delete(); // remove token expirado
            return response()->json(['message' => 'Token expired'], 401);
        }

        return $next($request);
    }
}