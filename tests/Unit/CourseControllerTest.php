<?php

namespace Tests\Unit;

use App\Course;
use App\Instructor;
use App\StudentGrade;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Helpers\Auth\SetupUser;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
  use SetupUser, RefreshDatabase, WithoutMiddleware;
  /**
   * Returns courses sorted by name
   *
   * @return void
   */
  public function testCourseIndexSortedByName()
  {
    $course1 = factory(Course::class)->create([
      "name" => "math",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      "name" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses");
    
    $response->assertJson([
      "current_page" => 1,
      "data" => [
        [ "id" => $course2->id ],
        [ "id" => $course1->id ]
      ],
      "per_page" => 10,
      "total" => 2,
    ]);
  }

  /**
   * Returns courses sorted by created_at in asending order
   *
   * @return void
   */
  public function testPaginatedCourseIndexSortedByCreatedAtInAsendingOrder()
  {
    $course1 = factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "created_at" => Carbon::now()->addDay(1)
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?sort=created_at");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $course1->id,
          ],
          [ 
            "id" => $course2->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns courses sorted by created_at descending order
   *
   * @return void
   */
  public function testPaginatedCourseIndexSortedByCreatedAtInDescendingOrder()
  {
    $course1 = factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "created_at" => Carbon::now()->addDay(1)
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?sort=-created_at");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $course2->id ],
          [ "id" => $course1->id ],
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns courses sorted by updated_at descending order
   *
   * @return void
   */
  public function testPaginatedCourseIndexSortedByUpdatedAtInDescendingOrder()
  {
    $course1 = factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "updated_at" => Carbon::now()->addDay(1)
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?sort=updated_at");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $course1->id ],
          [ "id" => $course2->id ],
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns courses sorted by updated_at ascending order
   *
   * @return void
   */
  public function testPaginatedCourseIndexSortedByUpdatedAtInAscendingOrder()
  {
    $course1 = factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "updated_at" => Carbon::now()->addDay(1)
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?sort=-updated_at");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $course2->id ],
          [ "id" => $course1->id ],
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns courses filtered by name
   *
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByName()
  {
    $course1 = factory(Course::class)->create([
      "name" => "math",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "name" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[name]=math");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $course1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns courses filtered by valid instructor_id
   *
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByValidInstructorId()
  {
    $instructor = factory(Instructor::class)->create();
    $course1 = factory(Course::class)->create([
      "name" => "math",
      "instructor_id" => $instructor->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      "name" => "english",
      "instructor_id" => $instructor->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[instructor_id]=$instructor->id");

    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $course2->id ],
          [ "id" => $course1->id ],
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns courses filtered by invalid instructor_id
   *
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByInvalidInstructorId()
  {
    $instructor = factory(Instructor::class)->create();
    factory(Course::class)->create([
      "name" => "math",
      "instructor_id" => $instructor->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "name" => "english",
      "instructor_id" => $instructor->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[instructor_id]=0");

    $response->assertStatus(422);
  }

  /**
   * Returns courses filtered by valid status
   *
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByValidStatus()
  {
    $course1 = factory(Course::class)->create([
      "name" => "math",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course1->update(['status_id' => 3]);
    factory(Course::class)->create([
      "name" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $courseStatus = array_flip(Course::Statuses)[3]; //complete

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[status]=$courseStatus");

    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $course1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns courses filtered by invalid status
   *
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByInvalidStatus()
  {
    factory(Course::class)->create([
      "name" => "math",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "name" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[status]=bad_status");

    $response->assertStatus(422);
  }

  /**
   * Returns courses filtered by valid status id
   *
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByValidStatusId()
  {
    $course1 = factory(Course::class)->create([
      "name" => "math",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course1->update(['status_id' => 3]);
    factory(Course::class)->create([
      "name" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[status_id]=3");

    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $course1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns courses filtered by invalid status id
   *
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByInvalidStatusId()
  {
    factory(Course::class)->create([
      "name" => "math",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "name" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[status_id]=0");

    $response
      ->assertStatus(422);
  }

  /**
   * Returns courses filtered by valid student_grade_id
   *
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByValidStudentGradeId()
  {
    $student_grade = StudentGrade::first();
    $course1 = factory(Course::class)->create([
      "name" => "math",
      "student_grade_id" => $student_grade->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      "name" => "english",
      "student_grade_id" => $student_grade->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[student_grade_id]=$student_grade->id");

    $response->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $course2->id ],
          [ "id" => $course1->id ],
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns courses filtered by invalid student_grade_id
   *
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByInvalidStudentGradeId()
  {
    $student_grade = StudentGrade::first();
    factory(Course::class)->create([
      "name" => "math",
      "student_grade_id" => $student_grade->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "name" => "english",
      "student_grade_id" => $student_grade->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[student_grade_id]=0");

    $response->assertStatus(422);
  }

  /**
   * Returns courses with an instructor
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByHasInstructor()
  {
    $instructor = factory(Instructor::class)->create();
    $course1 = factory(Course::class)->create([
      "name" => "math",
      "instructor_id" => $instructor->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      "name" => "english",
      "instructor_id" => $instructor->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "instructor_id" => null,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[has_instructor]=true");

    $response->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $course2->id ],
          [ "id" => $course1->id ],
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns courses without an instructor
   * @return void
   */
  public function testPaginatedCourseIndexFilteredByCoursesWithoutInstructor()
  {
    $instructor = factory(Instructor::class)->create();
    factory(Course::class)->create([
      "name" => "math",
      "instructor_id" => $instructor->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Course::class)->create([
      "name" => "english",
      "instructor_id" => $instructor->id,
      "tenant_id" => $this->user->tenant->id,
    ]);
    $course3 = factory(Course::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "instructor_id" => null,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/courses?filter[has_instructor]=false");

    $response->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $course3->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }
}
