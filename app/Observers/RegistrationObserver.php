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

    $registration->user->allow('view', $registration);
    $registration->user->allow('view', $registration->course);

    if ($registration->user->guardian) {
      $registration->user->guardian->allow('view', $registration);
    }
  }

  /**
   * Handle the registration "updated" event.
   *
   * @param  \App\Registration  $registration
   * @return void
   */
  public function updated(Registration $registration)
  {
      //
  }

  /**
   * Handle the registration "deleted" event.
   *
   * @param  \App\Registration  $registration
   * @return void
   */
  public function deleted(Registration $registration)
  {
      //
  }

  /**
   * Handle the registration "restored" event.
   *
   * @param  \App\Registration  $registration
   * @return void
   */
  public function restored(Registration $registration)
  {
      //
  }

  /**
   * Handle the registration "force deleted" event.
   *
   * @param  \App\Registration  $registration
   * @return void
   */
  public function forceDeleted(Registration $registration)
  {
      //
  }
}
