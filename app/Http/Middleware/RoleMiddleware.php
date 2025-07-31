<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            return response()->json(['message' => 'Token tidak valid atau kadaluarsa'], 401);
        }

        // Cek apakah role-nya sesuai
        if ($user->role !== $role) {
            return response()->json(['message' => 'Akses ditolak (bukan ' . $role . ')'], 403);
        }

        // Lolos, terusin request
        $request->merge(['user' => $user]);
        return $next($request);
    }
}

