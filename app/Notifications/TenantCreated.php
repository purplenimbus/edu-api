<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TenantCreated extends Notification
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
		$url = env('FRONT_END_URL');

		$first_name = ucfirst($notifiable->firstname);

		return (new MailMessage)
			->subject(__('registration.account_created', ['name' => config('app.name')]))
			->greeting(__('registration.hi', [ 'first_name' => $first_name ]))
			->line(__('registration.thanks', [ 'name' => $notifiable->tenant->name ]))
			->action(__('registration.login'), url("${url}auth/login"));
	}
}
