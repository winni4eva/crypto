<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class TwoFactorVerficationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $twilio = TwilioChannel::class;
    
        if (request()->has('option_2fa') && request()->get('option_2fa')){
            return request()->get('option_2fa') == 'phone' ? [$twilio] : ['mail'];
        }
        return $notifiable->option_2fa == 'phone' ? [$twilio] : ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('QHCoin - Two Factor Code')
            ->greeting("Hi {$notifiable->first_name}")
            ->level('info')
            ->line("Your Two Factor Authentication Code Is: {$notifiable->token_2fa}")
            ->line('Thank you for using our application!')
            ->salutation('Best wishes');
    }

    /**
     * Get the twilio representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NotificationChannels\Twilio\TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content("QHCoin - Two Factor Code: {$notifiable->token_2fa}");
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
