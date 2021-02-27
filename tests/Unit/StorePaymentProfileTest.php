<?php

namespace Tests\Unit;

use App\CourseGrade;
use App\PaymentProfile;
use App\PaymentProfileItem;
use App\SchoolTermType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\Auth\SetupUser;

class StorePaymentProfileTest extends TestCase
{
  use SetupUser, RefreshDatabase;

  /**
   * Create a new payment profile
   *
   * @return void
   */
  public function testCreatesANewPaymentProfileCorrectly()
  {  
    $courseGrade = CourseGrade::first();
    $termType = SchoolTermType::ofTenant($this->user->tenant->id)->first();

    $response = $this->actingAs($this->user)
      ->postJson('api/v1/payment_profiles', [
        'course_grade_id' => $courseGrade->id,
        'name' => 'default',
        'term_type_id' => $termType->id,
      ]);
    
    $response->assertOk();
  }

  /**
   * Update a existing payment profile
   *
   * @return void
   */
  public function testUpdatesAnExistingPaymentProfileCorrectly()
  {  
    $courseGrade = CourseGrade::first();
    $termType = SchoolTermType::ofTenant($this->user->tenant->id)->first();

    $paymentProfile = factory(PaymentProfile::class)->create([
      'course_grade_id' => $courseGrade->id,
      'term_type_id' => $termType->id,
      'tenant_id' => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->putJson("api/v1/payment_profiles/{$paymentProfile->id}", [
        'course_grade_id' => CourseGrade::get()->last()->id,
        'name' => 'new default',
        'term_type_id' => SchoolTermType::ofTenant($this->user->tenant->id)->get()->last()->id,
      ]);
    
    $response->assertOk();
  }

  /**
   * Delete an existing payment profile
   *
   * @return void
   */
  public function testDeletesAnExistingPaymentProfileCorrectly()
  {  
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
    
    $response->assertOk();
  }

  /**
   * Create a new payment profile
   *
   * @return void
   */
  public function testDoesentCreateAnExistingPaymentProfileWithDuplicate()
  {  
    $courseGrade = CourseGrade::first();
    $termType = SchoolTermType::ofTenant($this->user->tenant->id)->first();

    factory(PaymentProfile::class)->create([
      'course_grade_id' => $courseGrade->id,
      'term_type_id' => $termType->id,
      'tenant_id' => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/payment_profiles/", [
        'course_grade_id' => $courseGrade->id,
        'name' => 'new default',
        'term_type_id' => $termType->id,
      ]);
    
    $response->assertStatus(422);
  }
}
