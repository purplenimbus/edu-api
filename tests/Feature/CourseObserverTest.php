<?php

namespace Tests\Feature;

use App\Course;
use App\StudentGrade;
use App\Instructor;
use App\Jobs\SendStudentGrades;
use App\NimbusEdu\Institution;
use App\Registration;
use App\SchoolTerm;
use App\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Helpers\SetupUser;
use Tests\TestCase;
use Illuminate\Support\Facades\Bus;
use SubjectsSeeder;

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
  public function testSetsOvewrittenCourseAttributes()
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

    $instructor = factory(Instructor::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);

    $course = factory(Course::class)->create([
      'instructor_id' => $instructor->id,
      'tenant_id' => $this->user->tenant_id,
    ]);

    factory(Registration::class)->create([
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

  /**
   * Test update term status instructor
   *
   * @return void
   */
  public function testUpdatesCurrentTermStatus()
  {
    $this->seed(DatabaseSeeder::class);

    $studentGrade = StudentGrade::first();
    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $institution = new Institution();
    $institution->newSchoolTerm($this->user->tenant, 'first term');

    $this->actingAs($this->user)
      ->putJson("api/v1/courses/{$course->id}", [
        'student_grade_id' => $studentGrade->id,
        'status_id' => Course::Statuses['complete'],
      ]);

    $this->assertEquals(array_flip(SchoolTerm::Statuses)[2], SchoolTerm::ofTenant($this->user->tenant->id)->first()->status);
  }

  public function testDispatchesTheCompleteTermJobWhenTheCurrentTermCompleted()
  {
    Bus::fake();
    $this->seed(DatabaseSeeder::class);

    $studentGrade = StudentGrade::first();
    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $institution = new Institution();
    $institution->newSchoolTerm($this->user->tenant, 'first term');

    $this->actingAs($this->user)
      ->putJson("api/v1/courses/{$course->id}", [
        'student_grade_id' => $studentGrade->id,
        'status_id' => Course::Statuses['complete'],
      ]);

    Bus::assertDispatched(SendStudentGrades::class);
  }

  public function testDoesntDispatchTheCompleteTermJobWhenTheCurrentTermCompletedIfOtherCoursesAreinProgress()
  {
    Bus::fake();
    $this->seed(DatabaseSeeder::class);

    $studentGrade = StudentGrade::first();
    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $institution = new Institution();
    $institution->newSchoolTerm($this->user->tenant, 'first term');

    $this->actingAs($this->user)
      ->putJson("api/v1/courses/{$course->id}", [
        'student_grade_id' => $studentGrade->id,
        'status_id' => Course::Statuses['complete'],
      ]);

    Bus::assertNotDispatched(SendStudentGrades::class);
  }

  public function testDosentUpdateTheCurrentTermStatusIfOtherCoursesAreInProgress()
  {
    $this->seed(DatabaseSeeder::class);

    $studentGrade = StudentGrade::first();
    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $institution = new Institution();
    $institution->newSchoolTerm($this->user->tenant, 'first term');

    $this->actingAs($this->user)
      ->putJson("api/v1/courses/{$course->id}", [
        'student_grade_id' => $studentGrade->id,
        'status_id' => Course::Statuses['complete'],
      ]);

    $this->assertEquals(array_flip(SchoolTerm::Statuses)[1], SchoolTerm::ofTenant($this->user->tenant->id)->first()->status);
  }

  public function testSetsDefaultCourseSchema()
  {
    $this->seed(SubjectsSeeder::class);

    $subject = Subject::first();
    $studentGrade = StudentGrade::first();

    $this->actingAs($this->user)
      ->postJson("api/v1/courses", [
        "student_grade_id" => $studentGrade->id,
        "subject_id" => $subject->id,
      ])
      ->assertOk()
      ->assertJson([
        "schema" => [
          [
            "name" => "midterm 1",
            "score" => 20
          ],
          [
            "name" => "midterm 2",
            "score" => 20
          ],
          [
            "name" => "midterm 3",
            "score" => 20
          ],
          [
            "name" => "exam",
            "score" => 40
          ]
        ]
      ]);
  }

  public function testSetsCourseSchemaFromSettings()
  {
    $this->seed(SubjectsSeeder::class);

    $subject = Subject::first();
    $studentGrade = StudentGrade::first();

    $this->user->tenant->settings()->update('course_schema', [
      [
        "name" => "midterm 1",
        "score" => 100
      ],
    ]);

    $this->actingAs($this->user)
      ->postJson("api/v1/courses", [
        "student_grade_id" => $studentGrade->id,
        "subject_id" => $subject->id,
        "tenant_id" => $this->user->tenant_id
      ])
      ->assertOk()
      ->assertJson([
        "schema" => [
          [
            "name" => "midterm 1",
            "score" => 100
          ],
        ]
      ]);
  }
}
