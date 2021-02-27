<?php

namespace App\Observers;

use App\CourseGrade;
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
    $this->createDefaultPaymentItemTypes($tenant);
    $this->createDefaultSchoolTermTypes($tenant);
    $this->createDefaultStudentGrades($tenant);
  }

  private function createDefaultPaymentItemTypes(Tenant $tenant) {
    $defaultPaymentItemTypes = config('edu.default.payment_item_types');

    foreach($defaultPaymentItemTypes as $type) {
      $type = array_merge([
        'tenant_id' => $tenant->id,
      ], $type);

      PaymentProfileItemType::create($type);
    }
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

      CourseGrade::create($grade);
    }
  }
}
