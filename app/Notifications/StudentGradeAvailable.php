<?php

namespace App\Notifications;

use App\Guardian;
use App\SchoolTerm;
use App\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentGradeAvailable extends Notification
{
  use Queueable;

  private $schoolTerm;
  private $student;
  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(SchoolTerm $schoolTerm, Student $student)
  {
    $this->schoolTerm = $schoolTerm;
    $this->student = $student;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail', 'database'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    $key = is_a($notifiable, 'App\Student') ? 'student_subject' : 'guardian_subject';
    $host = env('FRONT_END_URL','http://localhost:4200/#/');
		$url = "{$host}messages";

    return (new MailMessage)
      ->subject(__("email.student_grade_available.{$key}", [
        'first_name' => ucfirst($this->student->firstname),
        'term_name' => $this->schoolTerm->name,
      ]))
      ->greeting(__('email.hi', [
        'first_name' => ucfirst($notifiable->firstname),
      ]))
      ->line(__('email.student_grade_available.message', [
        'first_name' => ucfirst($this->student->firstname),
        'term_name' => $this->schoolTerm->name,
      ]))
      ->action(__('email.student_grade_available.view_result'), url($url));
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray()
  {
    return [
      'message' => __('email.student_grade_available.message', [
        'first_name' => ucfirst($this->student->firstname),
        'term_name' => $this->schoolTerm->name,
      ])
    ];
  }
}
