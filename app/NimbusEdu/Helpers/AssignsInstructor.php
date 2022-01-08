<?php

namespace App\NimbusEdu\Helpers;

use App\Course;
use App\Instructor;
use App\User;

trait AssignsInstructor
{
  public function assignInstructor(Instructor $instructor, Course $course){
    $course->instructor_id = $instructor->id;

    $course->save();

    $instructor->account_status_id = User::StatusTypes["assigned"];

    $instructor->save();

    return $course;
  }
}