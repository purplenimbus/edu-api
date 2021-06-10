<?php

namespace Tests\Feature;

use App\Course;
use App\StudentGrade;
use App\NimbusEdu\Institution;
use App\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\SetupUser;
use Tests\TestCase;
use DatabaseSeeder;
use App\Student;

class RegistrationObserverTest extends TestCase
{
  use RefreshDatabase, SetupUser;
  /**
   * Test for default Course scores
   *
   * @return void
   */
  public function testDefaultCourseScores()
  {
    $this->seed(DatabaseSeeder::class);

    $school = new Institution();
    $school->newSchoolTerm($this->user->tenant, 'first term');

    $studentGrade = StudentGrade::first();

    $student = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);

    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'student_grade_id' => $studentGrade->id,
    ]);
    $course->update(['status_id'=> Course::Statuses['in progress']]);

    $this->actingAs($this->user)
      ->postJson("api/v1/registrations/batch", [
        'course_ids' => [$course->id],
        'student_ids' => [$student->id],
        'student_grade_id'=> $student->grade["id"]
      ])
      ->assertStatus(200);

    $this->assertContains(
      [
        'name' => 'midterm 1',
        'score' => 0
      ], Registration::first()->score->scores
    );
    
    $this->assertContains(
      [
        'name' => 'midterm 2',
        'score' => 0
      ], Registration::first()->score->scores
    );

    $this->assertContains(
      [
        'name' => 'midterm 3',
        'score' => 0
      ], Registration::first()->score->scores
    );

    $this->assertContains(
      [
        'name' => 'exam',
        'score' => 0
      ], 
      Registration::first()->score->scores
    );
  }

  public function testForDeletedRegistration(){
    $this->seed(DatabaseSeeder::class);

    $studentGrade = StudentGrade::first(); 

    $student = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);

    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);

    $registration = factory(Registration::class)->create([
      'course_id' => $course->id,
      'tenant_id' => $this->user->tenant_id,
      'user_id' => $student->id
    ]);

    $response = $this->actingAs($this->user)
      ->delete("api/v1/registrations", [
        'registration_ids' => [$registration->id],
      ]);

    $response->assertStatus(200);

    $this->assertEquals(Registration::count(), 0);

    $this->assertFalse($student->can('view', $course));
  }
}
