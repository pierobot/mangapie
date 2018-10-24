<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\NotificationRequest;
use App\WatchNotification;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index');
    }

    public function delete(NotificationRequest $request)
    {
        $action = \Request::get('action');
        $watchNotificationIds= \Request::get('ids');
        $watchNotifications = \Auth::user()->watchNotifications;

        if ($action === 'dismiss.selected') {
            if (empty($watchNotificationIds) == false) {
                foreach ($watchNotificationIds as $notificationId) {
                    $watchNotifications->find($notificationId)->forceDelete();
                }
            }
        } elseif ($action === 'dismiss.all') {
            foreach ($watchNotifications as $watchNotification) {
                $watchNotification->forceDelete();
            }
        }

        return redirect()->back();
    }
}
