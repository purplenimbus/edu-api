<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TermComplete extends Notification
{
  use Queueable;

  var $payload;
  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($payload)
  {
    $this->payload = $payload;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['broadcast', 'database'];
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
  public function toDatabase($notifiable)
  {
    return $this->payload;
  }

  /**
   * The event's broadcast name.
   *
   * @return string
   */
  public function broadcastAs()
  {
    return 'term.complete';
  }
}
