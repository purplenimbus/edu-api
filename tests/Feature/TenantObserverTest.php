<?php

namespace Tests\Feature;

use App\StudentGrade;
use App\SchoolTermType;
use App\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class TenantObserverTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  /**
   * Creates default school term types
   *
   * @return void
   */
  public function testItCreatesDefaultSchoolTermTypes()
  {
    $response = $this->postJson('/api/v1/register', [
      'email' => $this->faker->email,
      'fullName' => $this->faker->name,
      'name' => $this->faker->company,
      'password' => '1234abcd',
      'password_confirmation' => '1234abcd',
    ]);
    $tenant = Tenant::latest()->first();
    
    $response->assertStatus(200);
    $this->assertEquals(Arr::pluck(config('edu.default.school_terms'), 'name'), SchoolTermType::ofTenant($tenant->id)->first()->pluck('name')->toArray());
  }

  /**
   * Creates default student grades
   *
   * @return void
   */
  public function testItCreatesDefaultStudentGrades()
  {
    $response = $this->postJson('/api/v1/register', [
      'email' => $this->faker->email,
      'fullName' => $this->faker->name,
      'name' => $this->faker->company,
      'password' => '1234abcd',
      'password_confirmation' => '1234abcd',
    ]);

    $response->assertStatus(200);
    $this->assertEquals(Arr::pluck(config('edu.default.student_grades'), 'name'), StudentGrade::all()->pluck('name')->toArray());
  }
}
