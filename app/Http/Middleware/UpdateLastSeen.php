<?php

namespace App\Http\Middleware;

use Closure;

class UpdateLastSeen
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
        if (\Auth::check()) {
            \Auth::user()->update([
                'last_seen' => \Carbon\Carbon::now()
            ]);
        }

        return $next($request);
    }
}
