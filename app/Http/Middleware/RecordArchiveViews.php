<?php

namespace App\Http\Middleware;

use App\Jobs\IncreaseArchiveHeat;
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

            if (\Config::get('app.views.enabled') === true)
                \Queue::push(new IncrementArchiveViews($user, $archive));

            if (\Config::get('app.heat.enabled') === true)
                \Queue::push(new IncreaseArchiveHeat($user, $archive));
        }

        return $next($request);
    }
}
