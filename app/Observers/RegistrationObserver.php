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
        'registration_id' => $registration->id,
        'scores' => array_map(
          function ($item) {
            $item['score'] = 0;
            return $item;
          },
          $registration->course->schema
        ),
      ]);
      $registration->course_score_id = $course_score->id;
      $registration->save();
    }
    $this->grantAccessToRegistration($registration);
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
    $this->blockAccessToRegistration($registration);
  }

  private function grantAccessToRegistration(Registration $registration){
    $registration->user->allow('view', $registration);
    $registration->user->allow('view', $registration->course);

    if ($registration->user->guardian) {
      $registration->user->guardian->allow('view', $registration);
    }
  }

  private function blockAccessToRegistration(Registration $registration){
    $registration->user->disallow('view', $registration->course);

    if ($registration->user->guardian) {
      $registration->user->guardian->disallow('view', $registration);
    }
  }
}
