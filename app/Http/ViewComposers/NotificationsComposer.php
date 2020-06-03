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
        $user = \Auth::user();

        if (! empty($user)) {
            $notifications = $user->unreadNotifications()
                ->orderBy('updated_at')
                ->get();

            $view->with('notifications', $notifications);
        }
    }
}