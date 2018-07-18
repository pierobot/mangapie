<?php

namespace App\Http\Middleware;

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

            \Queue::push(new IncrementMangaViews($user, $manga));
        }

        return $next($request);
    }
}
