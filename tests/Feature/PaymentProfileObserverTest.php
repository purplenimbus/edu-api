<?php

namespace Tests\Feature;

use App\CourseGrade;
use App\PaymentProfile;
use App\PaymentProfileItem;
use App\SchoolTermType;
use CourseGradeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\Auth\SetupUser;
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
    $this->seed(CourseGradeSeeder::class);
  
    $courseGrade = CourseGrade::first();
    $termType = SchoolTermType::ofTenant($this->user->tenant->id)->first();

    $paymentProfile = factory(PaymentProfile::class)->create([
      'course_grade_id' => $courseGrade->id,
      'term_type_id' => $termType->id,
      'tenant_id' => $this->user->tenant->id,
    ]);

    factory(PaymentProfileItem::class, 3)->create([
      'payment_profile_id' => $paymentProfile->id,
      'tenant_id' => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->deleteJson("api/v1/payment_profiles/{$paymentProfile->id}");
    
    $this->assertEquals(0, PaymentProfile::count());
    $this->assertEquals(0, $paymentProfile->items->count());
  }
}
