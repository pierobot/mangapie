<?php

namespace App\Notifications;

use App\Archive;

use App\Scanner;
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
        $via = ['database'];

        if (config('app.mail_notifications') === true) {
            $via[] = 'mail';
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $seriesName = $this->series['name'];
        $archiveName = Scanner::simplifyName(Scanner::removeExtension($this->archive['name']));
        $finfo = new \SplFileInfo($this->archive['name']);
        $directory = $finfo->getPath() ?? 'Root';

        $readUrl = \URL::action('ReaderController@index', [
            $this->series['id'],
            $this->archive['id'],
            1,
            'notification' => $this->id
        ]);

        return (new MailMessage)
            ->subject("New archive for $seriesName")
            ->line("You have a new archive named \"$archiveName\" in \"$directory\".")
            ->action('Read now', $readUrl)
            ->line('Note: Reading will dismiss the notification in Mangapie.');
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
