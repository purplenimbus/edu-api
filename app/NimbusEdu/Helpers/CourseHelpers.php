<?php

namespace App\NimbusEdu\Helpers;

use App\Course;
use Illuminate\Support\Arr;

trait CourseHelpers
{
  public function parseCourseCode(Course $course) {
    $subjectCode = Arr::get($course, "subject.code", "");
    $StudentGrade = Arr::get($course, "grade.name", "");
  
    return strtoupper($subjectCode.'-'.str_replace(' ', '-', $StudentGrade));
  }
}