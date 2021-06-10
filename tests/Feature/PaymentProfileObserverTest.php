<?php

namespace Tests\Feature;

use App\StudentGrade;
use App\PaymentProfile;
use App\PaymentProfileItem;
use App\SchoolTermType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\SetupUser;
use Tests\TestCase;

class PaymentProfileObserverTest extends TestCase
{
  use SetupUser, RefreshDatabase;
  /**
   * Delete an existing payment profile
   *
   * @return void
   */
  public function testDeletesAnExistingPaymentProfileCorrectly()
  {  
    $studentGrade = StudentGrade::first();
    $schoolTermType = SchoolTermType::ofTenant($this->user->tenant->id)->first();

    $paymentProfile = factory(PaymentProfile::class)->create([
      'student_grade_id' => $studentGrade->id,
      'school_term_type_id' => $schoolTermType->id,
      'tenant_id' => $this->user->tenant->id,
    ]);

    factory(PaymentProfileItem::class, 3)->create([
      'payment_profile_id' => $paymentProfile->id,
      'tenant_id' => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->deleteJson("api/v1/payment_profiles/{$paymentProfile->id}");
    
    $this->assertEquals(0, PaymentProfile::count());
    $this->assertEquals(0, $paymentProfile->items->count());
  }
}
