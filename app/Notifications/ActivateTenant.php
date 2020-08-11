<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class ActivateTenant extends VerifyEmail
{
  use Queueable;

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
		$host = env('FRONT_END_URL','http://localhost/');
		
		$url = "{$host}auth/login";

		$first_name = ucfirst($notifiable->owner->firstname);

		$email = ucfirst($notifiable->owner->email);

		return (new MailMessage)
			->line(__('registration.hi', [ 'first_name' => $first_name ]))
			->line(__('registration.one_step'))
			->line(__('registration.before'))
			->action(__('registration.email', [ 'email' => strtolower($email) ]), url('/'));
	}
}
