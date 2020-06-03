<?php

namespace App\Notifications;

use App\Archive;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

final class NewArchiveNotification
    extends Notification
    implements ShouldQueue
{
    use Queueable;

    /** @var array $series An array that contains the id and name of the series. */
    private $series;
    /** @var array $archives An array that contains the id and name of archive. */
    private $archive;

    /**
     * Create a new notification instance.
     *
     * @param Archive $archive
     * @return void
     */
    public function __construct(Archive $archive)
    {
        $this->series = [
            'id' => $archive->manga->id,
            'name' => $archive->manga->name,
        ];

        $this->archive = [
            'id' => $archive->id,
            'name' => $archive->name
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'series' => $this->series,
            'archive' => $this->archive,
        ];
    }
}
