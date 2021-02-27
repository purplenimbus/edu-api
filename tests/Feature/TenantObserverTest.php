<?php

namespace Tests\Feature;

use App\CourseGrade;
use App\PaymentProfileItemType;
use App\SchoolTermType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class TenantObserverTest extends TestCase
{
  use RefreshDatabase, WithFaker;
  /**
   * Creates a default payment profile items
   *
   * @return void
   */
  public function testCreateDefaultPaymentProfileItemTypes()
  {
    $response = $this->postJson('/api/v1/register', [
      'email' => $this->faker->email,
      'fullName' => $this->faker->name,
      'name' => $this->faker->company,
      'password' => '1234abcd',
      'password_confirmation' => '1234abcd',
    ]);

    $response->assertStatus(200);
    $this->assertEquals(Arr::pluck(config('edu.default.payment_item_types'), 'name'), PaymentProfileItemType::all()->pluck('name')->toArray());
  }

  /**
   * Creates a default school term types
   *
   * @return void
   */
  public function testCreateDefaultSchoolTermTypes()
  {
    $response = $this->postJson('/api/v1/register', [
      'email' => $this->faker->email,
      'fullName' => $this->faker->name,
      'name' => $this->faker->company,
      'password' => '1234abcd',
      'password_confirmation' => '1234abcd',
    ]);

    $response->assertStatus(200);
    $this->assertEquals(Arr::pluck(config('edu.default.school_terms'), 'name'), SchoolTermType::all()->pluck('name')->toArray());
  }

  /**
   * Creates a default school term types
   *
   * @return void
   */
  public function testCreateDefaultStudentGrades()
  {
    $response = $this->postJson('/api/v1/register', [
      'email' => $this->faker->email,
      'fullName' => $this->faker->name,
      'name' => $this->faker->company,
      'password' => '1234abcd',
      'password_confirmation' => '1234abcd',
    ]);

    $response->assertStatus(200);
    $this->assertEquals(Arr::pluck(config('edu.default.student_grades'), 'name'), CourseGrade::all()->pluck('name')->toArray());
  }
}
