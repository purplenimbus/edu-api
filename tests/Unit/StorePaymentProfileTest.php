<?php

namespace Tests\Unit;

use App\StudentGrade;
use App\PaymentProfile;
use App\PaymentProfileItem;
use App\SchoolTermType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\SetupUser;

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
    $studentGrade = StudentGrade::first();
    $termType = SchoolTermType::ofTenant($this->user->tenant->id)->first();

    $response = $this->actingAs($this->user)
      ->postJson('api/v1/payment_profiles', [
        'student_grade_id' => $studentGrade->id,
        'name' => 'default',
        'school_term_type_id' => $termType->id,
      ]);
    
    $response->assertOk();
  }

  /**
   * Create a new payment profile
   *
   * @return void
   */
  public function testCreatesANewPaymentProfileCorrectlyForANewTerm()
  {  
    $studentGrade = StudentGrade::first();
    $termType = SchoolTermType::ofTenant($this->user->tenant->id)->first();
    $termType2 = SchoolTermType::ofTenant($this->user->tenant->id)->get()->last();

    factory(PaymentProfile::class)->create([
      'student_grade_id' => $studentGrade->id,
      'school_term_type_id' => $termType->id,
      'tenant_id' => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->postJson('api/v1/payment_profiles', [
        'student_grade_id' => $studentGrade->id,
        'name' => 'default',
        'school_term_type_id' => $termType2->id,
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
    $studentGrade = StudentGrade::first();
    $termType = SchoolTermType::ofTenant($this->user->tenant->id)->first();

    $paymentProfile = factory(PaymentProfile::class)->create([
      'student_grade_id' => $studentGrade->id,
      'school_term_type_id' => $termType->id,
      'tenant_id' => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->putJson("api/v1/payment_profiles/{$paymentProfile->id}", [
        'student_grade_id' => StudentGrade::get()->last()->id,
        'name' => 'new default',
        'school_term_type_id' => SchoolTermType::ofTenant($this->user->tenant->id)->get()->last()->id,
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
    $studentGrade = StudentGrade::first();
    $termType = SchoolTermType::ofTenant($this->user->tenant->id)->first();

    $paymentProfile = factory(PaymentProfile::class)->create([
      'student_grade_id' => $studentGrade->id,
      'school_term_type_id' => $termType->id,
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
    $studentGrade = StudentGrade::first();
    $termType = SchoolTermType::ofTenant($this->user->tenant->id)->first();
    $termType2 = SchoolTermType::ofTenant($this->user->tenant->id)->get()->last();

    factory(PaymentProfile::class)->create([
      'student_grade_id' => $studentGrade->id,
      'school_term_type_id' => $termType->id,
      'tenant_id' => $this->user->tenant->id,
    ]);
    factory(PaymentProfile::class)->create([
      'student_grade_id' => $studentGrade->id,
      'school_term_type_id' => $termType2->id,
      'tenant_id' => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/payment_profiles/", [
        'student_grade_id' => $studentGrade->id,
        'name' => 'new default',
        'school_term_type_id' => $termType2->id,
      ]);
    
    $response->assertStatus(422);
  }
}
