<?php

namespace Tests\Helpers;

use App\Course;
use App\Registration;
use App\SchoolTerm;
use App\Student;

trait RegisterStudent
{
  function registerStudent(SchoolTerm $schoolTerm = null, Student $student, Course $course) {
    $data = [
      'course_id' => $course->id,
      'tenant_id' => $student->tenant_id,
      'user_id' => $student->id,
    ];

    if (!is_null($schoolTerm)) {
      $data['term_id'] = $schoolTerm->id;
    }
  
    return factory(Registration::class)->create($data);
  }
}
