<?php

namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class NotificationComposer
{
    public function __construct()
    {
    }

    /**
     * Binds the relevant notification information to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        if (\Auth::check()) {

            $user = \Auth::user()->load('watchNotifications');
            $notificationCount = 0;

            $watchNotifications = $user->watchNotifications;
            $notificationCount += $watchNotifications->count();

            $view->with('watchNotifications', $watchNotifications)
                 ->with('notificationCount', $notificationCount);
        }
    }
}