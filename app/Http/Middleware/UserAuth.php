<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class UserAuth
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
        // $data = json_decode(request()->getContent());
        // $email = $data->auth_email;
        // $password = $data->auth_password;

        $email = $request->header('auth_email');
        $password = $request->header('auth_password');
        // dd($password);
        $user = User::where('email', $email)->where('password', $password)->first();
        // dd($user);
        if(is_null($user)){
            return response()->json(['message' => 'Authentication error!'], 401);
        }
        $request->attributes->set('auth_user', $user);
        return $next($request);
    }
}
