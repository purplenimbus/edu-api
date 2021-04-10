<?php

namespace App\Observers;

use App\StudentGrade;
use App\PaymentProfileItemType;
use App\SchoolTermType;
use App\Tenant;

class TenantObserver
{
  /**
   * Handle the tenant "created" event.
   *
   * @param  \App\Tenant  $tenant
   * @return void
   */
  public function created(Tenant $tenant)
  {
    $this->createDefaultSchoolTermTypes($tenant);
    $this->createDefaultStudentGrades($tenant);
  }

  private function createDefaultSchoolTermTypes(Tenant $tenant) {
    $defaultSchoolTermTypes = config('edu.default.school_terms');

    foreach($defaultSchoolTermTypes as $type) {
      $type = array_merge([
        'tenant_id' => $tenant->id,
      ], $type);

      SchoolTermType::create($type);
    }
  }

  private function createDefaultStudentGrades(Tenant $tenant) {
    $defaultStudentGrades = config('edu.default.student_grades');

    foreach($defaultStudentGrades as $grade) {
      $grade = array_merge([
        'tenant_id' => $tenant->id,
      ], $grade);

      StudentGrade::create($grade);
    }
  }
}
