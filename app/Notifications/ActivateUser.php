<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class ActivateUser extends VerifyEmail
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
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    if (static::$toMailCallback) {
      return call_user_func(static::$toMailCallback, $notifiable);
    }

    $first_name = ucfirst($notifiable->firstname);

    $email = ucfirst($notifiable->email);

    return (new MailMessage)
      ->subject(__('registration.welcome', ['name' => config('app.name')]))
      ->greeting(__('registration.hi', ['first_name' => $first_name]))
      ->line(new HtmlString(__('registration.invited', [
        'school_owner' => $notifiable->tenant->owner->fullname ?? '',
        'school_name' => $notifiable->tenant->name ?? ''
      ])))
      ->line(__('registration.confirm'))
      ->action(__('registration.email', ['email' => strtolower($email)]), $this->verificationUrl($notifiable));
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
