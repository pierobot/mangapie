<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\NotificationRequest;
use App\WatchNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $watchNotifications = \Auth::user()->watchNotifications;

        $notificationCount = $watchNotifications->count();

        return view('notifications.index')
               ->with('notificationCount', $notificationCount)
               ->with('watchNotifications', $watchNotifications);
    }

    public function dismiss(NotificationRequest $request)
    {
        // remove all the given watch notifications
        $watchNotifications = \Request::get('watch');
        if (empty($watchNotifications) == false) {
            foreach ($watchNotifications as $notificationId) {
                \Auth::user()->watchNotifications->find($notificationId)->forceDelete();
            }
        }

        return \Response::make('', 200);
    }
}
