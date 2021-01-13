<?php

namespace Tests\Feature;

use App\Course;
use App\CourseGrade;
use App\CourseStatus;
use App\Nimbus\Institution;
use App\Registration;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Feature\Helpers\Auth\SetupUser;
use Tests\TestCase;
use Bouncer;
use DatabaseSeeder;
use App\Student;
use Silber\Bouncer\Bouncer as BouncerBouncer;

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
    $school->newSchoolTerm($this->user->tenant);

    $courseGrade = CourseGrade::first();

    $student = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'meta' => [
        'course_grade_id' => $courseGrade->id,
      ],
    ]);

    $courseStatus = CourseStatus::whereName('in progress')->first();

    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'course_grade_id' => $courseGrade->id,
    ]);
    $course->update(['status_id'=> $courseStatus->id]);

   $response =  $this->actingAs($this->user)
    ->postJson("api/v1/registrations/batch", [
      'course_ids' => [$course->id],
      'student_ids' => [$student->id],
      'course_grade_id'=> $student->grade["id"]
    ]);

    $response->assertStatus(200);

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

    $courseGrade = CourseGrade::first(); 

    $student = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'meta' => [
        'course_grade_id' => $courseGrade->id,
      ],
    ]);

    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);

    $registration = factory(Registration::class)->create([
      'course_id' => $course->id,
      'tenant_id' => $this->user->tenant_id,
      'user_id' => $this->user
    ]);

    dd($registration->toArray());
    $response = $this->actingAs($this->user)
      ->delete("api/v1/registrations", [
        'registration_ids' => [$registration->id],
      ]);

    //Bouncer::refreshFor($student);

    $response->assertStatus(200);

    $this->assertEquals(Registration::where('id', $registration->id), 0);

    $this->assertFalse($student->can('view', $course));
  }
}
