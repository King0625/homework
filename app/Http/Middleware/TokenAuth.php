<?php

namespace App\Http\Middleware;

use Closure;

class TokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api_token = $request->header('api_token');
        $auth_user = User::where('api_token', $api_token)->get();
        // dd($auth_user);

        if(!count($auth_user)){
            return response()->json(['message' => 'Authentication error'], 401);
        }
        $request->attributes->set('auth_user', $auth_user);
        return $next($request);
    }
}
