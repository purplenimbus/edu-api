<?php

namespace App\NimbusEdu\Helpers;

use App\Guardian;
use App\Instructor;
use App\Student;
use App\Tenant;

trait importsUser
{
  public function importStudent(array $data, Tenant $tenant) {
    $student = Student::with(['status_type'])
      ->firstOrNew(array_only($data, ['firstname','lastname','email','tenant_id']));
    $student->tenant_id = $tenant->id;
    $student->meta = ['student_grade_id' => $data['student_grade_id']];
    $student->save();

    return $student->toArray();
  }

  public function importInstructor(array $data, Tenant $tenant) {
    $instructor = Instructor::with(['status_type'])
      ->firstOrNew(array_only($data, ['firstname','lastname','email','tenant_id']));
    $instructor->tenant_id = $tenant->id;
    $instructor->save();

    return $instructor->toArray();
  }

  public function importGuardian(array $data, Tenant $tenant) {
    $guardian = Guardian::with(['status_type'])
      ->firstOrNew(array_only($data, ['firstname','lastname','email','tenant_id']));
    $guardian->tenant_id = $tenant->id;
    $guardian->save();

    return $guardian->toArray();
  }
}