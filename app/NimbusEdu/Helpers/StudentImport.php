<?php

namespace App\NimbusEdu\Helpers;

use App\Student;
use App\Tenant;

trait StudentImport
{
  public function importStudent(array $data, Tenant $tenant) {
    $student = Student::with(['status_type'])
      ->firstOrNew(array_only($data, ['firstname','lastname','email','tenant_id']));
    $student->tenant_id = $tenant->id;
    $student->meta = ['student_grade_id' => $data['student_grade_id']];
    $student->save();

    return $student->toArray();
  }
}