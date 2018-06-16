<?php

namespace App\Http\Requests;

use App\WatchNotification;
use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (\Auth::check() == false)
            return false;

        $ownsNotifications = true;

        $notificationIds = \Input::get('ids');
        foreach ($notificationIds as $notificationId) {
            $owns = WatchNotification::find($notificationId) !== null;
            if ($owns == false) {
                $ownsNotifications = false;
                break;
            }
        }

        return $ownsNotifications === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action' => 'required|string|in:dismiss.selected,dismiss.all',

            'ids' => 'array',
            'ids.*' => 'integer|exists:watch_notifications,id',
        ];
    }
}
