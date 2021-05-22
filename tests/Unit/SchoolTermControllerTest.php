<?php

namespace Tests\Unit;

use App\Course;
use App\Instructor;
use App\Nimbus\Institution;
use App\Registration;
use App\SchoolTerm;
use App\Student;
use App\StudentGrade;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\Auth\SetupUser;

class SchoolTermControllerTest extends TestCase
{
	use RefreshDatabase, SetupUser, WithoutMiddleware;
	/**
   * Get all school terms for a tenant
   *
   * @return void
   */
  public function testGetSchoolTerms()
  {
    $this->seed(DatabaseSeeder::class);
		$institution = new Institution();
    $schoolTerm = $institution->newSchoolTerm($this->user->tenant, 'first term');
		$response = $this->actingAs($this->user)
      ->getJson('api/v1/school_terms');

    $response->assertStatus(200)
			->assertJson([
				"data" => [
					[
						"id" => $schoolTerm->id,
						"name" => $schoolTerm->name,
					],
				],
			]);
  }

	/**
   * Get the courses offered this term
   *
   * @return void
   */
  public function testGetSchoolCourses()
  {
    $this->seed(DatabaseSeeder::class);
		$institution = new Institution();
    $schoolTerm = $institution->newSchoolTerm($this->user->tenant, 'first term');
    factory(Course::class)->create([
      'term_id' => $schoolTerm->id,
      'tenant_id' => $this->user->tenant_id,
    ]);
		$response = $this->actingAs($this->user)
      ->getJson('api/v1/school_terms?include=coursesCount');

    $response->assertStatus(200)
			->assertJson([
				"data" => [
					[
						"courses_count" => 1,
					],
				],
			]);
  }

	/**
   * Get the registrations offered this term
   *
   * @return void
   */
  public function testGetSchoolRegistrations()
  {
    $this->seed(DatabaseSeeder::class);
		$institution = new Institution();
    $schoolTerm = $institution->newSchoolTerm($this->user->tenant, 'first term');
    $studentGrade = StudentGrade::first();
    $student = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
    $course = factory(Course::class)->create([
      'term_id' => $schoolTerm->id,
      'tenant_id' => $this->user->tenant_id,
      'student_grade_id' => $studentGrade->id,
    ]);
		$this->registerStudent($schoolTerm, $student, $course);
		$response = $this->actingAs($this->user)
      ->getJson('api/v1/school_terms?include=registrationsCount');

    $response->assertStatus(200)
			->assertJson([
				"data" => [
					[
						"registrations_count" => 1,
					],
				],
			]);
  }

	/**
   * Get the students enrolled this term
   *
   * @return void
   */
  public function testGetSchoolTermRegisteredStudents()
  {
    $this->seed(DatabaseSeeder::class);
		$institution = new Institution();
    $schoolTerm = $institution->newSchoolTerm($this->user->tenant, 'first term');
    $studentGrade = StudentGrade::first();
    $student1 = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
		factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
    $course = factory(Course::class)->create([
      'term_id' => $schoolTerm->id,
      'tenant_id' => $this->user->tenant_id,
      'student_grade_id' => $studentGrade->id,
    ]);
		$this->registerStudent($schoolTerm, $student1, $course);
		$response = $this->actingAs($this->user)
      ->getJson("api/v1/school_terms/{$schoolTerm->id}?append=registered_students_count");

    $response->assertStatus(200)
			->assertJson([
				"registered_students_count" => 1,
			]);
  }

	/**
   * Get the intructors assigned this term
   *
   * @return void
   */
  public function testGetSchoolTermAssignedInstructors()
  {
    $this->seed(DatabaseSeeder::class);
		$institution = new Institution();
    $schoolTerm = $institution->newSchoolTerm($this->user->tenant, 'first term');
    $instructor1 = factory(Instructor::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);
		factory(Instructor::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);
    $course = factory(Course::class)->create([
      'term_id' => $schoolTerm->id,
      'tenant_id' => $this->user->tenant_id,
    ]);
		$instructor1->assignInstructor($course);
		$response = $this->actingAs($this->user)
      ->getJson("api/v1/school_terms/{$schoolTerm->id}?append=assigned_instructors_count");

		$response->assertStatus(200)
			->assertJson([
				"assigned_instructors_count" => 1,
			]);
  }

	/**
   * Create a school term
   *
   * @return void
   */
  public function testCreateSchoolTerm()
  {
    $this->seed(DatabaseSeeder::class);
		$data = factory(SchoolTerm::class)->make([
			'type_id' => $this->user->tenant->schoolTermTypes->first()->id,
			'tenant_id' => $this->user->tenant->id,
		]);
		$response = $this->actingAs($this->user)
      ->postJson('api/v1/school_terms', $data->toArray());
		$schoolTerm = SchoolTerm::first();

		$response->assertStatus(200)
			->assertJson([
				"id" => $schoolTerm->id,
				"name" => $schoolTerm->name,
			]);
  }

	private function registerStudent(SchoolTerm $schoolTerm, Student $student, Course $course) {
    return factory(Registration::class)->create([
      'course_id' => $course->id,
      'tenant_id' => $this->user->tenant_id,
      'term_id' => $schoolTerm->id,
      'user_id' => $student->id,
    ]);
  }
}
