<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$positions): Response
    {
        $userPosition = (string) Auth::user()->position;

        \Log::info('Positions parameter received: ' . json_encode($positions));

        $allowedPositions = array_map('strval', $positions);  // Treat all parameters as strings

        \Log::info('User position: ' . $userPosition);
        \Log::info('Allowed positions: ' . json_encode($allowedPositions));

        if (in_array($userPosition, $allowedPositions, true)) {
            return $next($request);
        }
        return response()->json(['message' => 'You do not have the required role to access this route.'], 403);
    }
}
