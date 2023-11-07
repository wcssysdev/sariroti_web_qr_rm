<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$role)
    {
        if (!$request->session()->has('id') || !$request->session()->has('name') || !$request->session()->has('user_role') || !$request->session()->has('user_initial_name') || !$request->session()->has('app_name') || !$request->session()->has('plant')) {
            return redirect("/");
        }

        if (array_search(session("user_role"),$role) === false) {
            return redirect("/");
        }
        return $next($request);
    }
}
