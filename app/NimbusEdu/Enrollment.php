<?php

namespace App\NimbusEdu;

use App\Registration;
use App\Student;
use App\Tenant;
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
    $registration = Registration::firstOrCreate([
      'tenant_id' => $this->tenant->id ,
      'user_id' => $student->id,
      'course_id' => $course_id,
      'term_id' => $this->tenant->has_current_term ? $this->tenant->current_term->id : null,
    ]);

    return $registration;
  }
}
