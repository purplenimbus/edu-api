<?php

namespace App\Notifications;

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
   * Get the notification"s delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ["mail", "database"];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    $subjectKey = is_a($notifiable, "App\Student") ? "student_subject" : "guardian_subject";
    $messageKey = is_a($notifiable, "App\Student") ? "student_message" : "guardian_message";

    return (new MailMessage)
      ->subject(__("email.student_grade_available.{$subjectKey}", [
        "first_name" => ucfirst($this->student->firstname),
        "term_name" => $this->schoolTerm->name,
      ]))
      ->greeting(__("email.hi", [
        "first_name" => ucfirst($notifiable->firstname),
      ]))
      ->line(__("email.student_grade_available.{$messageKey}", [
        "first_name" => ucfirst($this->student->firstname),
        "term_name" => $this->schoolTerm->name,
      ]))
      ->action(__("email.student_grade_available.view_result"), url(env("FRONT_END_URL","http://localhost:4200/#/")));
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    $messageKey = is_a($notifiable, "App\Student") ? "student_message" : "guardian_message";

    return [
      "message" => __("email.student_grade_available.{$messageKey}", [
        "first_name" => ucfirst($this->student->firstname),
        "term_name" => $this->schoolTerm->name,
      ])
    ];
  }
}
