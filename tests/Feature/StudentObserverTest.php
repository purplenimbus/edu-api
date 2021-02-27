<?php

namespace Tests\Feature;

use App\StudentGrade;
use App\Student;
use Carbon\Carbon;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\Auth\SetupUser;
use Tests\TestCase;

class StudentObserverTest extends TestCase
{
  use RefreshDatabase, SetupUser;

  /**
   * Test the default password
   *
   * @return void
   */
  public function testSetsDefaultPassword()
  {
    $this->seed(DatabaseSeeder::class);

    $this->user->tenant->setOwner($this->user);

    $person = factory(Student::class)->make([
      'date_of_birth' => Carbon::now()->toString(),
      'student_grade_id' => StudentGrade::first()->id,
    ]);

    $this
      ->actingAs($this->user)
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
   * Test the default password
   *
   * @return void
   */
  public function testSetsStudentId()
  {
    $this->seed(DatabaseSeeder::class);

    $this->user->tenant->setOwner($this->user);

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
   * Test the default role
   *
   * @return void
   */
  public function testSetsTheUsersRole()
  {
    $this->seed(DatabaseSeeder::class);
  
    $this->user->tenant->setOwner($this->user);

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
