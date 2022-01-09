<?php

namespace App\Observers;

use App\Instructor;
use App\Notifications\ActivateUser;
use App\User;

class InstructorObserver
{

  /**
   * Handle the instructor "creating" event.
   *
   * @param  \App\Instructor  $instructor
   * @return void
   */
  public function creating(Instructor $instructor) {
    $instructor->password = $instructor->createDefaultPassword();
  }
  
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

    $instructor->notify(new ActivateUser);
  }

}
