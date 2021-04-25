<?php

namespace Tests\Unit;

use App\StudentGrade;
use App\PaymentProfile;
use App\PaymentProfileItem;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\Auth\SetupUser;

class PaymentProfileControllerTest extends TestCase
{
  use RefreshDatabase, SetupUser, WithoutMiddleware;
  /**
   * Get a tenants payment profiles
   *
   * @return void
   */
  public function testGetTenantPaymentProfile()
  {
    $response = $this->actingAs($this->user)
      ->getJson('api/v1/payment_profiles');

    $response->assertStatus(200);
  }

  /**
   * Create a tenant payment profile
   *
   * @return void
   */
  public function testCreateTenantPaymentProfiles()
  {
    $response = $this->actingAs($this->user)
      ->postJson('api/v1/payment_profiles', [
        'name' => 'default',
      ]);

    $response->assertStatus(200);
    $this->assertEquals('default', PaymentProfile::first()->name);
  }

  /**
   * Update a tenant payment profile
   *
   * @return void
   */
  public function testUpdateTenantPaymentProfile()
  {
    $payment_profile = PaymentProfile::create([
      'name' => 'old default',
      'tenant_id' => $this->user->tenant->id
    ]);

    $response = $this->actingAs($this->user)
      ->putJson("api/v1/payment_profiles/{$payment_profile->id}", [
        'name' => 'new default',
      ]);

    $response->assertStatus(200);
    $this->assertEquals('new default', PaymentProfile::first()->name);
  }

  /**
   * Update a tenant payment profile with payment profile items
   *
   * @return void
   */
  public function testUpdateTenantPaymentProfileItems()
  {
    $payment_profile = PaymentProfile::create([
      'name' => 'old default',
      'tenant_id' => $this->user->tenant->id
    ]);

    $response = $this->actingAs($this->user)
      ->putJson("api/v1/payment_profiles/{$payment_profile->id}", [
        'items' => [
          [
            'amount' => 100,
            'description' => 'test',
            'type' => PaymentProfileItem::Types["administrative"],
          ],
          [
            'amount' => 200,
            'description' => 'test 2',
            'type' => PaymentProfileItem::Types["administrative"],
          ],
          [
            'amount' => 150,
            'description' => 'test 3',
            'type' => PaymentProfileItem::Types["tuition"],
          ],
        ],
      ]);

    $response->assertStatus(200);
    $this->assertEquals(3, PaymentProfile::first()->items->count());
    $this->assertEquals(450, PaymentProfile::first()->total);
  }

  /**
   * Update a tenant payment profile with a course grade
   *
   * @return void
   */
  public function testUpdateTenantPaymentProfileWithStudentGrade()
  {
    $this->seed(DatabaseSeeder::class);

    $payment_profile = PaymentProfile::create([
      'name' => 'old default',
      'tenant_id' => $this->user->tenant->id
    ]);

    $studentGrade = StudentGrade::first();

    $response = $this->actingAs($this->user)
      ->putJson("api/v1/payment_profiles/{$payment_profile->id}", [
        'student_grade_id' => $studentGrade->id,
      ]);

    $response->assertStatus(200);
    $this->assertEquals(1, PaymentProfile::first()->whereStudentGradeId($studentGrade->id)->count());
  }

  /**
   * Update a tenant payment profile with a course grade
   *
   * @return void
   */
  public function testUpdateTenantPaymentProfileWithTermType()
  {
    $this->seed(DatabaseSeeder::class);

    $payment_profile = PaymentProfile::create([
      'name' => 'old default',
      'tenant_id' => $this->user->tenant->id
    ]);

    $termType = $this->user->tenant->term_types()->first();

    $response = $this->actingAs($this->user)
      ->putJson("api/v1/payment_profiles/{$payment_profile->id}", [
        'school_term_type_id' => $termType->id,
      ]);

    $response->assertStatus(200);
    $this->assertEquals(1, PaymentProfile::first()->whereSchoolTermTypeId($termType->id)->count());
  }

  /**
   * create a tenant payment profile with a course grade
   *
   * @return void
   */
  public function createTenantPaymentProfileWithTermType()
  {
    $this->seed(DatabaseSeeder::class);

    $termType = $this->user->tenant->term_types()->first();

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/payment_profiles/", [
        'name' => 'old default',
        'school_term_type_id' => $termType->id,
      ]);

    $response->assertStatus(200);
    $this->assertEquals(1, PaymentProfile::first()->whereSchoolTermTypeId($termType->id)->count());
  }

  /**
   * Create a tenant payment profile with a course grade and type type
   *
   * @return void
   */
  public function testCreateTenantPaymentProfileWithStudentGradeAndTermType()
  {
    $this->seed(DatabaseSeeder::class);

    $termType = $this->user->tenant->term_types()->first();
    $studentGrade = StudentGrade::first();

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/payment_profiles", [
        'student_grade_id' => $studentGrade->id,
        'school_term_type_id' => $termType->id,
        'name' => 'test',
      ]);

    $response->assertStatus(200);
    $this->assertEquals(1, PaymentProfile::first()->where([
      'student_grade_id' => $studentGrade->id,
      'school_term_type_id' => $termType->id,
    ])->count());
  }

  /**
   * Update a tenant payment profile with a course grade and term type
   *
   * @return void
   */
  public function testUpdateTenantPaymentProfileWithStudentGradeAndTermType()
  {
    $this->seed(DatabaseSeeder::class);

    $payment_profile = factory(PaymentProfile::class)->create([
      'name' => 'old default',
      'tenant_id' => $this->user->tenant->id
    ]);

    $termType = $this->user->tenant->term_types()->first();
    $studentGrade = StudentGrade::first();

    $response = $this->actingAs($this->user)
      ->putJson("api/v1/payment_profiles/{$payment_profile->id}", [
        'student_grade_id' => $studentGrade->id,
        'school_term_type_id' => $termType->id,
      ]);

    $response->assertStatus(200);
    $this->assertEquals(1, PaymentProfile::first()->where([
      'student_grade_id' => $studentGrade->id,
      'school_term_type_id' => $termType->id,
    ])->count());
  }
}
