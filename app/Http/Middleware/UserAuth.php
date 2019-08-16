<?php

namespace App\Http\Middleware;

use Closure;


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
        $data = json_decode(request()->getContent());
        $email = $data->email;
        $password = $data->password;
        dd($data);
        return $next($request);
    }
}
