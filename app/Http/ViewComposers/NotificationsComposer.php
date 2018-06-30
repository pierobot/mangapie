<?php

namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class NotificationsComposer
{
    /**
     * Binds the relevant notification information to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = \Auth::user()->load('watchNotifications');

        if (auth()->check()) {
            $notificationCount = 0;

            $watchNotifications = $user->watchNotifications;
            $notificationCount += $watchNotifications->count();

            $view->with('watchNotifications', $watchNotifications)
                 ->with('notificationCount', $notificationCount);
        }
    }
}