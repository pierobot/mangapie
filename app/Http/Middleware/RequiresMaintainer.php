<?php

namespace App\Http\Middleware;

use Closure;

class RequiresMaintainer
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
        if (\Auth::user()->isAdmin() == false && \Auth::user()->isMaintainer() == false)
            return \Redirect::action('HomeController@index')->withErrors(['You do not have permission to view that page.']);

        return $next($request);
    }
}
