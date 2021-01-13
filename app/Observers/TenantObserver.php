<?php

namespace App\Observers;

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
}
