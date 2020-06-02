<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notifications\DeleteArchiveNotificationsRequest;
use App\Notifications\NewArchiveNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return $this->archives();
    }

    /**
     * View for archive notifications.
     * TODO: Find a way to avoid a duplicate query with shared.notifications view.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function archives()
    {
        $user = \Auth::user();
        $notifications = $user->unreadNotifications()
            ->orderBy('updated_at')
            ->get();

        $archiveNotifications = $notifications->where('type', '=', NewArchiveNotification::class);

        return view('notifications.archives')
            ->with('archiveNotifications', $archiveNotifications);
    }

    public function destroyArchiveNotifications(DeleteArchiveNotificationsRequest $request)
    {
        $seriesIds = $request->get('series');

        \Auth::user()
            ->unreadNotifications()
            ->where('type', NewArchiveNotification::class)
            ->where(function (Builder $query) use ($seriesIds) {
                foreach ($seriesIds as $id) {
                    $query->orWhereJsonContains('data->series->id', $id);
                }
            })
            ->forceDelete();

        return redirect()->back()->with('success', 'The selected archive notifications were dismissed.');
    }

    public function destroyAllArchiveNotifications()
    {
        \Auth::user()
            ->unreadNotifications()
            ->where('type', NewArchiveNotification::class)
            ->forceDelete();

        return redirect()->back()->with('success', 'All archive notifications were dismissed.');
    }
}
