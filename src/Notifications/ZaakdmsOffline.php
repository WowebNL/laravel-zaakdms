<?php

namespace Woweb\Zaakdms\Notifications;

use Exception;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class ZaakdmsOffline extends Notification
{
    private $exception;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(?Exception $e = null)
    {
        $this->exception = $e;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack', 'mail'];
    }

    public function toSlack($notifiable)
    {
        $e = $this->exception;
        return (new SlackMessage())
            ->error()
            ->to(config('app.settings.slack_channel'))
            ->content('Zaakdms inactive at ' . config('app.name') . ' (' . config('app.url') . ')')
            ->attachment(function (SlackAttachment $attachment) use ($e) {
                $attachment->title('Exception stacktrace')
                    ->field(function ($field) use ($e) {
                        $field->title('Trace:')
                            ->content($e->getMessage());
                    });
            });
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Message
     */
    public function toMail($notifiable)
    {
        $e = $this->exception;
        return (new MailMessage)
            ->error()
            ->subject(config('app.name') . ' ' . __('zaakdms offline!'))
            ->line('De zaakdms service is niet te bereikbaar voor ' . config('app.name'))
            ->line('Exception: ' . $e->getMessage());
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
            //
        ];
    }
}
