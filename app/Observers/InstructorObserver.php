<?php

namespace App\Observers;

use App\Instructor;
use App\User;
use App\StatusType;

class InstructorObserver
{
  /**
   * Handle the instructor "created" event.
   *
   * @param  \App\Instructor  $instructor
   * @return void
   */
  public function created(Instructor $instructor)
  {
    $user = User::find($instructor->id);
    $user->assign('instructor');//Assign user model a role to return roles and permissions for JWT Claims
    $instructor->assign('instructor');
  }

  /**
   * Handle the instructor "updated" event.
   *
   * @param  \App\Instructor  $instructor
   * @return void
   */
  public function updated(Instructor $instructor)
  {
      //
  }

  /**
   * Handle the instructor "deleted" event.
   *
   * @param  \App\Instructor  $instructor
   * @return void
   */
  public function deleted(Instructor $instructor)
  {
      //
  }

  /**
   * Handle the instructor "restored" event.
   *
   * @param  \App\Instructor  $instructor
   * @return void
   */
  public function restored(Instructor $instructor)
  {
      //
  }

  /**
   * Handle the instructor "force deleted" event.
   *
   * @param  \App\Instructor  $instructor
   * @return void
   */
  public function forceDeleted(Instructor $instructor)
  {
      //
  }
}
