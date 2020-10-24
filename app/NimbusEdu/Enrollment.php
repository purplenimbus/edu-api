<?php

namespace App\Nimbus;

use App\Registration;
use App\Invoice;
use App\Student;
use App\Tenant;
use App\Guardian;
use App\Course;

class Enrollment
{
  public $tenant;

  public function __construct(Tenant $tenant)
  {
    $this->tenant = $tenant;
  }

  public function enrollStudents(Array $student_ids, Array $course_ids) {
    $students = Student::find($student_ids);
    $courses = Course::find($course_ids);

    return $students->map(function($student) use ($courses) {
      return $courses->map(function($course) use ($student) {
        return $this->enroll($student, $course->id);
      });
    });
  }

  private function enroll(Student $student, Int $course_id) {
    $school_term = $this->tenant->current_term;

    $registration = Registration::firstOrCreate([
      'tenant_id' => $this->tenant->id ,
      'user_id' => $student->id,
      'course_id' => $course_id,
      'term_id' => $school_term->id,
    ]);

    return $registration;
  }
}
