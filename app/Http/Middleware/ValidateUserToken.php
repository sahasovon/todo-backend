<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ValidateUserToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasCookie('x-user-token')) {
            return response()->json([
                'error' => 'User token is not valid'
            ], 400);
        }

        $userToken = Cookie::get('x-user-token');

        // Add user token to request
        $request->merge(compact('userToken'));

        return $next($request);
    }
}
