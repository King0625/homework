<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class BasicAuth
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
        // dd(Auth::onceBasic());
        $email = $request->getUser();
        $password = $request->getPassword();
        $user = User::where('email', $email)->where('password', $password)->first();
        if(is_null($user)){
            return response()->json(['message' => 'Authentication error!'], 401);
        }
        $request->attributes->set('auth_user', $user);
        return $next($request);
    }
}
