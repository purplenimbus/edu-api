<?php

namespace Tests\Unit;

use App\Course;
use App\NimbusEdu\Institution;
use App\Registration;
use App\Student;
use App\StudentGrade;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\SetupUser;

class StudentControllerTest extends TestCase
{
  use SetupUser, RefreshDatabase, WithoutMiddleware;
  /**
   * Return students sorted by first name.
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsSortedByName()
  {
    $student1 = factory(Student::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "firstname" => "diana",
      "tenant_id" => $this->user->tenant->id,
    ]);
    
    $this->actingAs($this->user)
      ->getJson("api/v1/students")
      ->assertJson([
      "current_page" => 1,
      "data" => [
        [ "id" => $student1->id ],
        [ "id" => $student2->id ]
      ],
      "per_page" => 10,
      "total" => 2,
    ]);
  }

  /**
   * Returns students sorted by created_at in asending order
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsSortedByCreatedAtInAsendingOrder()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "created_at" => Carbon::now()->tomorrow()
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?sort=created_at")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $student1->id,
          ],
          [ 
            "id" => $student2->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns students sorted by created_at in desending order
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsSortedByCreatedAtInDesendingOrder()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "created_at" => Carbon::now()->tomorrow()
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?sort=-created_at")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $student2->id,
          ],
          [ 
            "id" => $student1->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns students sorted by updated_at in asending order
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsSortedByUpdatedAtInAsendingOrder()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "updated_at" => Carbon::now()->tomorrow()
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?sort=updated_at")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $student1->id,
          ],
          [ 
            "id" => $student2->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns students sorted by updated_at in desending order
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsSortedByUpdatedAtInDesendingOrder()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "updated_at" => Carbon::now()->tomorrow()
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?sort=-updated_at")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $student2->id,
          ],
          [ 
            "id" => $student1->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns students filtered by firstname
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsFilteredByFirstName()
  {
    $student1 = factory(Student::class)->create([
      "firstname" => "diana",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Student::class)->create([
      "firstname" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[firstname]=diana")->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students filtered by firstname
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsFilteredByLastName()
  {
    $student1 = factory(Student::class)->create([
      "lastname" => "diana",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Student::class)->create([
      "lastname" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[lastname]=diana")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students filtered by email
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsFilteredByEmail()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[email]=$student1->email")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students filtered by student id
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsFilteredByStudentId()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[student_id]=$student1->student_id")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students filtered by image
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsFilteredByImage()
  {
    $student1 = factory(Student::class)->create([
      "image" => "http://www.thisisanimage.com",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[has_image]=true")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students with out an image
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsWithOutImage()
  {
    factory(Student::class)->create([
      "image" => "http://www.thisisanimage.com",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[has_image]=false")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students by student grade
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsFilteredByStudentGrade()
  {
    $studentGrade1 = StudentGrade::whereAlias('js 1')->first();
    $studentGrade2 = StudentGrade::whereAlias('ss 3')->first();
    factory(Student::class)->create([
      "meta" => [ "student_grade_id" => $studentGrade1->id ],
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student1 = factory(Student::class)->create([
      "meta" => [ "student_grade_id" => $studentGrade2->id ],
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[student_grade_id]=$studentGrade2->id")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns filtered by account status
   *
   * @return void
   */
  public function testItReturnsPaginatedStudentsFilteredByAccountStatus()
  {
    $accountStatus = Student::StatusTypes['registered'];
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student1->update(['account_status_id' => $accountStatus]);
    factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[account_status]=$accountStatus")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Update a valid student
   * @return void
   */
  public function testItUpdatesAValidStudent() {
    $student = factory(Student::class)->create([
      'firstname' => 'johnny',
      'tenant_id' => $this->user->tenant->id,
    ]);
    $this->actingAs($this->user)
      ->putJson("api/v1/students/$student->id", [
        'firstname' => 'english',
      ])
      ->assertOk()
      ->assertJson([
        'id' => $student->id,
        'firstname' => 'english',
      ]);
  }

  /**
   * Update an invalid student
   * @return void
   */
  public function testItDoesntUpdateAnInvalidStudent() {
    $this->actingAs($this->user)
      ->putJson("api/v1/students/0", [
        'firstname' => 'english',
      ])
      ->assertStatus(422);
  }

  /**
   * Create a valid student
   * @return void
   */
  public function testItCreatesAValidStudent() {
    $studentGrade = StudentGrade::whereAlias('js 1')->first();
    $data = factory(Student::class)->make([
      'firstname' => 'english',
      'student_grade_id' => $studentGrade->id,
      'tenant_id' => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->postJson("api/v1/students", $data->toArray())
      ->assertOk()
      ->assertJson([
        'email' => $data->email,
        'firstname' => 'english',
      ]);
  }

  /**
   * Create a student with invalid data
   * @return void
   */
  public function testItDoesntCreateAStudentFromInvalidData() {
    $data = factory(Student::class)->make([
      'firstname' => 'english',
      'tenant_id' => $this->user->tenant->id,
    ]);
    $this->actingAs($this->user)
      ->postJson("api/v1/students", $data->toArray())
      ->assertStatus(422);
  }

  /**
   * Show a valid student
   * @return void
   */
  public function testItShowsAValidStudent() {
    $student = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $this->actingAs($this->user)
      ->getJson("api/v1/students/$student->id")
      ->assertOk()
      ->assertJson([
        'email' => $student->email,
        'id' => $student->id,
      ]);
  }

  /**
   * Show a invalid student
   * @return void
   */
  public function testItDoesntShowAnInvalidStudent() {
    $this->actingAs($this->user)
      ->getJson("api/v1/students/0")
      ->assertStatus(422);
  }

  /**
   * Show eligible courses
   * @return void
   */
  public function testItReturnsEligibleCoursesForAStudent() {
    $institution = new Institution();
    $schoolTerm = $institution->newSchoolTerm($this->user->tenant, 'first term');
    $studentGrade1 = StudentGrade::whereAlias('js 1')->first();
    $studentGrade2 = StudentGrade::whereAlias('js 2')->first();
    $course1 = factory(Course::class)->create([
      'student_grade_id' => $studentGrade1->id,
      'tenant_id' => $this->user->tenant_id,
      'term_id' => $schoolTerm->id,
    ]);
    $course2 = factory(Course::class)->create([
      'student_grade_id' => $studentGrade1->id,
      'tenant_id' => $this->user->tenant_id,
      'term_id' => $schoolTerm->id,
    ]);
    factory(Course::class)->create([
      'student_grade_id' => $studentGrade2->id,
      'tenant_id' => $this->user->tenant_id,
      'term_id' => $schoolTerm->id,
    ]);
    $student = factory(Student::class)->create([
      'meta' => [ 'student_grade_id' => $studentGrade1->id],
      'tenant_id' => $this->user->tenant_id,
    ]);
    $this->enrollStudent($student, $course1);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students/$student->id/valid_courses")
      ->assertOk()
      ->assertJson([
        'data' => [
          $course2->only('id'),
        ],
        'total' => 1,
      ]);
  }

  private function enrollStudent(Student $student, Course $course) {
    $tenant = $student->tenant;

    return factory(Registration::class)->create([
      'course_id' => $course->id,
      'term_id' => $tenant->current_term->id,
      'tenant_id' => $tenant->id,
      'user_id' => $student->id,
    ]);
  }
}
