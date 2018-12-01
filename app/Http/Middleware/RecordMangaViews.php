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
        $manga = $request->route('manga');

        if (auth()->check() && ! empty($manga)) {
            $user = auth()->user();

            if (\Cache::tags(['config', 'views'])->get('enabled', true) == true) {
                \Queue::push(new IncrementMangaViews($user, $manga));
            }

            if (\Cache::tags(['config', 'heat'])->get('enabled', false) == true)
                \Queue::push(new IncreaseMangaHeat($user, $manga));
        }

        return $next($request);
    }
}
