<?php

namespace Tests\Feature;

use App\Course;
use App\StudentGrade;
use App\Instructor;
use App\Registration;
use App\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Helpers\Auth\SetupUser;
use Tests\TestCase;

class CourseObserverTest extends TestCase
{
  use RefreshDatabase, SetupUser, WithoutMiddleware;
  /**
   * Test default course name
   *
   * @return void
   */
  public function testSetsDefaultCourseName()
  {
    $this->seed(DatabaseSeeder::class);

    $subject = Subject::first();
    $studentGrade = StudentGrade::first();

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/courses", [
        "student_grade_id" => $studentGrade->id,
        "subject_id" => $subject->id,
      ]);

    $response->assertStatus(200)
      ->assertJson([
        'name' => $subject->name,
      ]);
  }

  /**
   * Test default course code
   *
   * @return void
   */
  public function testSetsDefaultCourseCode()
  {
    $this->seed(DatabaseSeeder::class);

    $subject = Subject::first();
    $studentGrade = StudentGrade::first();

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/courses", [
        "student_grade_id" => $studentGrade->id,
        "subject_id" => $subject->id,
      ]);

    $response->assertStatus(200)
      ->assertJson([
        'code' => "{$subject->code}-PRIMARY-1",
      ]);
  }

  /**
   * Test default course status
   *
   * @return void
   */
  public function testSetsDefaultCourseStatus()
  {
    $this->seed(DatabaseSeeder::class);

    $subject = Subject::first();
    $studentGrade = StudentGrade::first();

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/courses", [
        "student_grade_id" => $studentGrade->id,
        "subject_id" => $subject->id,
      ]);

    $response->assertStatus(200)
      ->assertJson([
        'status_id' => Course::Statuses['created'],
      ]);
  }

  /**
   * Test overrwitten default course attributes
   *
   * @return void
   */
  public function testSetsOverrittenCourseAttributes()
  {
    $this->seed(DatabaseSeeder::class);

    $subject = Subject::first();
    $studentGrade = StudentGrade::first();

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/courses", [
        'code' => 'CODE',
        'student_grade_id' => $studentGrade->id,
        'name' => 'course name',
        'subject_id' => $subject->id,
      ]);

    $response->assertStatus(200)
      ->assertJson([
        'code' => 'CODE',
        'name' => 'course name',
      ]);
  }

  /**
   * Test course instructor
   *
   * @return void
   */
  public function testSetsCourseInstructor()
  {
    $this->seed(DatabaseSeeder::class);

    $subject = Subject::first();
    $studentGrade = StudentGrade::first();
    $instructor = factory(Instructor::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/courses", [
        'student_grade_id' => $studentGrade->id,
        'subject_id' => $subject->id,
        'instructor_id' => $instructor->id
      ]);

    $response->assertStatus(200)
      ->assertJson([
        'instructor_id' => $instructor->id
      ]);
  }

  /**
   * Test deleting course
   *
   * @return void
   */
  public function testDeletesRegisteredStudents()
  {
    $this->seed(DatabaseSeeder::class);

    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);

    factory(Registration::class, 3)->create([
      'course_id' => $course->id,
      'tenant_id' => $this->user->tenant_id,
    ]);

    $response = $this->actingAs($this->user)
      ->delete("api/v1/courses", [
        'course_ids' => [$course->id],
      ]);

    $response->assertStatus(200);

    $this->assertEquals(Registration::count(), 0);
  }
}
