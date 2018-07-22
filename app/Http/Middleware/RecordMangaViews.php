<?php

namespace App\Http\Middleware;

use App\Jobs\IncreaseMangaHeat;
use App\Jobs\IncrementMangaViews;
use Closure;

class RecordMangaViews
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
            $manga = $request->route('manga');

            if (\Config::get('app.views.enabled') === true)
                \Queue::push(new IncrementMangaViews($user, $manga));

            if (\Config::get('app.heat.enabled') === true)
                \Queue::push(new IncreaseMangaHeat($user, $manga));
        }

        return $next($request);
    }
}
