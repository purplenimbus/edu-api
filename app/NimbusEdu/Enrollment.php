<?php

namespace App\Nimbus;

use App\Registration;
use App\Billing;
use App\Student;
use App\Tenant;

class Enrollment
{
  public $tenant;
  private $registrations;

  public function __construct(Tenant $tenant)
  {
    $this->tenant = $tenant;
    $this->registrations = [];
  }

  public function getRegistrations() {
    return $this->registrations;
  }

  public function enrollStudents($student_ids, $course_id) {
    $students = Student::find($student_ids);
    foreach ($students as $student) {
      array_push($this->registrations, $this->enroll($student, $course_id));
    }

    return $this->getRegistrations();
  }

  public function enroll(Student $student, $course_id) {
    $school_term = $this->tenant->current_term;

    $billing = Billing::firstOrCreate([
      'tenant_id' => $this->tenant->id,
      'student_id' => $student->id,
      'term_id' => $school_term->id
    ]);

    $registration = Registration::firstOrCreate([
      'tenant_id' => $this->tenant->id ,
      'user_id' => $student->id,
      'course_id' => $course_id,
      'term_id' => $school_term->id,
      'billing_id' => $billing->id
    ]);

    return $registration;
  }
}
