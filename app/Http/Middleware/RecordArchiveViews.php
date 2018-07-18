<?php

namespace App\Http\Middleware;

use App\Jobs\IncrementArchiveViews;
use Closure;

class RecordArchiveViews
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
        if (auth()->check()) {
            $user = auth()->guard()->user();
            $archive = $request->route('archive');

            \Queue::push(new IncrementArchiveViews($user, $archive), null, 'increment_views');
        }

        return $next($request);
    }
}
