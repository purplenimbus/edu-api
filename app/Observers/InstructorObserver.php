<?php

namespace App\Observers;

use App\Instructor;
use App\User;
use App\StatusType;

class InstructorObserver
{

  /**
   * Handle the instructor "creating" event.
   *
   * @param  \App\Instructor  $instructor
   * @return void
   */
  public function creating(Instructor $instructor){
    $instructor->password = $instructor->createDefaultPassword();
    $status_type = StatusType::where('name', 'created')->first();

    if (!is_null($status_type)) {
      $instructor->account_status_id = $status_type->id;
    }
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
  }

}
