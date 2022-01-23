<?php

namespace App\Observers;

use App\NimbusEdu\Institution;
use App\StudentGrade;
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
    $this->createDefaultCurriculum($tenant);
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

  private function createDefaultCurriculum(Tenant $tenant) {
    $institution = new Institution();

    $institution->generateCurriculum($tenant);
  }
}
