<?php

namespace App\Observers;

use App\Registration;
use App\CourseScore;

class RegistrationObserver
{
  /**
   * Handle the registration "created" event.
   *
   * @param  \App\Registration  $registration
   * @return void
   */
  public function created(Registration $registration)
  {
    if ($registration->course->schema) {
      $course_score = CourseScore::create([
        "registration_id" => $registration->id,
        "scores" => array_map(
          function ($item) {
            $item["score"] = 0;
            return $item;
          },
          $registration->course->schema
        ),
      ]);
      $registration->course_score_id = $course_score->id;
      $registration->save();
    }

    $registration->user->setRegistrationPermissions($registration);
  }

  /**
   * Handle the registration "deleting" event.
   *
   * @param  \App\Registration  $registration
   * @return void
   */
  public function deleting(Registration $registration){
    if ($registration->course_score) {
      $registration->course_score->delete();
    }
    $registration->user->revokeCoursePermissions($registration);
  }
}
