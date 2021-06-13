<?php

namespace Tests\Feature;

use App\StudentGrade;
use App\Student;
use Carbon\Carbon;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\SetupUser;
use Tests\TestCase;

class StudentObserverTest extends TestCase
{
  use RefreshDatabase, SetupUser;

  /**
   * Test the default password
   *
   * @return void
   */
  public function testItSetsTheDefaultPassword()
  {
    $this->seed(DatabaseSeeder::class);

    $person = factory(Student::class)->make([
      'date_of_birth' => Carbon::now()->toString(),
      'student_grade_id' => StudentGrade::first()->id,
    ]);

    $this->actingAs($this->user)
      ->postJson('api/v1/students', $person->only([
        'student_grade_id',
        'date_of_birth',
        'email',
        'firstname',
        'lastname',
      ]));

    $this->assertNotEmpty(Student::first()->password);
  }

  /**
   * Test the default student id
   *
   * @return void
   */
  public function testItSetsTheDefaultStudentIdIfNotPresent()
  {
    $this->seed(DatabaseSeeder::class);

    $person = factory(Student::class)->make([
      'date_of_birth' => Carbon::now()->toIso8601String(),
      'student_grade_id' => StudentGrade::first()->id,
    ]);

    $this->actingAs($this->user)
      ->postJson('api/v1/students', $person->only([
        'student_grade_id',
        'date_of_birth',
        'email',
        'firstname',
        'lastname',
      ]));

    $student = Student::first();

    $year = Carbon::now()->year;

    $this->assertEquals("{$year}00{$student->id}", $student->ref_id);
  }

  /**
   * Test the custom student id
   *
   * @return void
   */
  public function testItSetsTheCustomStudentId()
  {
    $this->seed(DatabaseSeeder::class);

    $person = factory(Student::class)->make([
      'date_of_birth' => Carbon::now()->toIso8601String(),
      'ref_id' => '111111',
      'student_grade_id' => StudentGrade::first()->id,
    ]);

    $this->actingAs($this->user)
      ->postJson('api/v1/students', $person->only([
        'student_grade_id',
        'date_of_birth',
        'email',
        'firstname',
        'lastname',
        'ref_id'
      ]));

    $student = Student::first();

    $year = Carbon::now()->year;

    $this->assertEquals("111111", $student->ref_id);
  }

  /**
   * Test the default role
   *
   * @return void
   */
  public function testItSetsTheUsersRole()
  {
    $this->seed(DatabaseSeeder::class);
  
    $person = factory(Student::class)->make([
      'date_of_birth' => Carbon::now()->toString(),
      'student_grade_id' => StudentGrade::first()->id,
    ]);

    $this->actingAs($this->user)
      ->postJson("api/v1/students",
        $person->only([
          'student_grade_id',
          'date_of_birth',
          'email',
          'firstname',
          'lastname',
        ]));

    $this->assertEquals('student', Student::first()->type);
  }
}
