<?php

namespace App\Observers;

use App\Student;
use App\User;

class StudentObserver
{
  /**
   * Handle the student "creating" event.
   *
   * @param  \App\Student  $instructor
   * @return void
   */
  public function creating(Student $student) {
    $student->password = $student->createDefaultPassword();
    $student->account_status_id = User::StatusTypes['unenrolled'];
  }

  /**
   * Handle the student "created" event.
   *
   * @param  \App\Student  $instructor
   * @return void
   */
  public function created(Student $student) {
    $user = User::find($student->id);
    $user->assign('student');//Assign user model a role to return roles and permissions for JWT Claims
    $student->assign('student');

    if (is_null($student->ref_id)) {
      $student->ref_id = $student->generateStudentId();
      $student->save();
    }
  }
}
