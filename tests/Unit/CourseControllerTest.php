<?php

namespace Tests\Unit;

use App\Course;
use App\Instructor;
use App\Registration;
use App\Student;
use App\StudentGrade;
use App\Subject;
use Carbon\Carbon;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Helpers\SetupUser;
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

  /**
   * Update a valid course
   * @return void
   */
  public function testUpdateValidCourse() {
    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->putJson("api/v1/courses/$course->id", [
        'name' => 'english',
      ]);

    $response->assertOk()
      ->assertJson([
        'id' => $course->id,
        'name' => 'english',
        'tenant_id' => $this->user->tenant_id,
      ]);
  }
  
  /**
   * Update an invalid course
   * @return void
   */
  public function testUpdateInvalidCourse() {
    factory(Course::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->putJson("api/v1/courses/0", [
        'name' => 'english',
      ]);

    $response->assertStatus(422);
  }

  /**
   * Create a valid course
   * @return void
   */
  public function testCreateValidCourse() {
    $data = factory(Course::class)->make([
      'tenant_id' => $this->user->tenant_id,
    ]);
    $response = $this->actingAs($this->user)
      ->postJson("api/v1/courses", $data->toArray());
    $course = Course::first();

    $response->assertOk()
      ->assertJson([
        'id' => $course->id,
        'name' => $course->name,
        'tenant_id' => $this->user->tenant_id,
      ]);
    $this->assertEquals(1, Course::count());
  }

  /**
   * Create a course with invalid data
   * @return void
   */
  public function testCreateValidCourseInvalidData() {
    $data = factory(Course::class)->make([
      'tenant_id' => $this->user->tenant_id,
    ]);
    $data = $data->toArray();
    $data['instructor_id'] = 0;

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/courses", $data);

    $response->assertStatus(422);
  }

  /**
   * show a valid course
   * @return void
   */
  public function testShowValidCourse() {
    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/courses/$course->id")
      ->assertOk()
      ->assertJson([
        'id' => $course->id,
        'name' => $course->name,
        'tenant_id' => $this->user->tenant_id,
      ]);
  }

  /**
   * show an invalid course
   * @return void
   */
  public function testShowInvalidCourse() {
    factory(Course::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/courses/0")
      ->assertStatus(422);
  }

  /**
   * show un registered students
   * @return void
   */
  public function testShowUnregisteredStudents() {
    $studentGrade = StudentGrade::first();
    $studentData = [
      'meta' => [ 'student_grade_id' => $studentGrade->id ],
      'tenant_id' => $this->user->tenant_id,
    ];

    $student1 = factory(Student::class)->create($studentData);
    $student2 = factory(Student::class)->create($studentData);
    $student3 = factory(Student::class)->create($studentData);

    $course = factory(Course::class)->create([
      'student_grade_id' => $studentGrade->id,
      'tenant_id' => $this->user->tenant_id,
    ]);

    factory(Registration::class)->create(['course_id' => $course->id, 'user_id' => $student1->id, 'tenant_id' => $this->user->tenant_id,]);
    factory(Registration::class)->create(['course_id' => $course->id, 'user_id' => $student2->id, 'tenant_id' => $this->user->tenant_id,]);

    $this->actingAs($this->user)
      ->getJson("api/v1/courses/not_registered?course_id=$course->id")
      ->assertOk()
      ->assertJson([
        'data' => [
          [
            'id' => $student3->id,
            'firstname' => $student3->firstname,
            'lastname' => $student3->lastname,
          ]
        ],
        'total' => 1,
      ]);
  }

  /**
   * show course statuses
   * @return void
   */
  public function testGetCourseStatuses() {
    $this->actingAs($this->user)
      ->getJson("api/v1/course_statuses")
      ->assertOk()
      ->assertJson(Course::Statuses);
  }

  /**
   * delete a valid courses
   * @return void
   */
  public function testDeleteValidCourses() {
    $course = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);
    $this->actingAs($this->user)
      ->deleteJson("api/v1/courses/", ['course_ids' => [$course->id]])
      ->assertOk();
  }

  /**
   * delete an invalid course
   * @return void
   */
  public function testDeleteInvalidCourses() {
    $this->actingAs($this->user)
      ->deleteJson("api/v1/courses/", ['course_ids' => [0]])
      ->assertStatus(422);
  }

  /**
   * batch create courses with valid subjects
   * @return void
   */
  public function testBatchCreateCoursesWithValidSubjects() {
    $this->seed(DatabaseSeeder::class);
    $subject1 = Subject::whereName('english')->first();
    $subject2 = Subject::whereName('mathematics')->first();
    $subject3 = Subject::whereName('biology')->first();

    $studentGrade1 = StudentGrade::whereAlias('js 1')->first();
    $studentGrade2 = StudentGrade::whereAlias('js 2')->first();
    $course1 = factory(Course::class)->make([
      'subject_id' => $subject1->id,
      'student_grade_id' => $studentGrade1->id,
      'tenant_id' => $this->user->tenant_id,
    ]);
    $course2 = factory(Course::class)->make([
      'subject_id' => $subject2->id,
      'student_grade_id' => $studentGrade2->id,
      'tenant_id' => $this->user->tenant_id,
    ]);
    $course3 = factory(Course::class)->create([
      'subject_id' => $subject3->id,
      'student_grade_id' => $studentGrade2->id,
      'tenant_id' => $this->user->tenant_id,
    ]);

    $this->actingAs($this->user)
      ->postJson("api/v1/courses/batch", [
        'data' => [
          $course1->only(['subject_id','student_grade_id']),
          $course2->only(['subject_id','student_grade_id']),
          $course3->only(['subject_id','student_grade_id']),
        ]
      ])
      ->assertOk()
      ->assertJson([
        'created' => [
          $course1->only(['subject_id','student_grade_id']),
          $course2->only(['subject_id','student_grade_id']),
        ],
        'updated' => [
          $course3->only(['subject_id','student_grade_id']),
        ],
      ]);
  }

  /**
   * batch create courses with invalid subjects
   * @return void
   */
  public function testBatchCreateCoursesWithInvalidSubjects() {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs($this->user)
      ->postJson("api/v1/courses/batch", [
        'data' => [
          ['subject_id' => 0],
        ]
      ])
      ->assertStatus(422);
  }

  /**
   * batch create courses with invalid student grades
   * @return void
   */
  public function testBatchCreateCoursesWithInvalidStudentGrades() {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs($this->user)
      ->postJson("api/v1/courses/batch", [
        'data' => [
          ['student_grade_id' => 0],
        ]
      ])
      ->assertStatus(422);
  }
}
