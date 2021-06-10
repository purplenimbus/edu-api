<?php

namespace Tests\Unit\Models;

use App\Course;
use App\NimbusEdu\Institution;
use App\Registration;
use App\SchoolTerm;
use App\SchoolTermType;
use App\Student;
use App\StudentGrade;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\SetupUser;

class SchoolTermTest extends TestCase
{
  use RefreshDatabase, SetupUser, WithoutMiddleware;

  /**
   * Get school term status
   *
   * @return void
   */
  public function testSchoolTermStatus()
  {
    $this->seed(DatabaseSeeder::class);
    $institution = new Institution();
    $schoolTerm = $institution->newSchoolTerm($this->user->tenant, 'first term');
    $schoolTerm->update([
      'status_id' => SchoolTerm::Statuses['complete'],
    ]);

    $this->assertEquals('complete', $schoolTerm->status);
  }

  /**
   * Get school term status
   *
   * @return void
   */
  public function testSchoolTermType()
  {
    $this->seed(DatabaseSeeder::class);
    $institution = new Institution();
    $schoolTerm = $institution->newSchoolTerm($this->user->tenant, 'first term');
    $schoolTermType = factory(SchoolTermType::class)->create();
    $schoolTerm->update([
      'type_id' => $schoolTermType->id,
    ]);

    $this->assertEquals($schoolTermType->id, $schoolTerm->type->id);
  }

  /**
   * registrations
   *
   * @return void
   */
  public function testRegistrations()
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
      'tenant_id' => $this->user->tenant_id,
      'student_grade_id' => $studentGrade->id,
    ]);
    $this->registerStudent($schoolTerm, $student, $course);

    $this->assertEquals(1, $schoolTerm->registrations()->count());
  }

  /**
   * registered students
   *
   * @return void
   */
  public function testRegisteredStudentsCount()
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
    $student2 = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
    $course1 = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'student_grade_id' => $studentGrade->id,
    ]);
    $course2 = factory(Course::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'student_grade_id' => $studentGrade->id,
    ]);
    $this->registerStudent($schoolTerm, $student1, $course1);
    $this->registerStudent($schoolTerm, $student2, $course2);

    $this->assertEquals(2, $schoolTerm->registered_students_count);
  }

  /**
   * instructors
   *
   * @return void
   */
  public function testAssignedInstructorsCount()
  {
    $this->seed(DatabaseSeeder::class);
    $institution = new Institution();
    $schoolTerm1 = $institution->newSchoolTerm($this->user->tenant, 'first term');
    $schoolTerm2 = $institution->newSchoolTerm($this->user->tenant, 'second term');
    $studentGrade = StudentGrade::first();
    $instructor1 = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);
    $instructor2 = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);
    $instructor3 = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
    ]);
    $student1 = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
    $student2 = factory(Student::class)->create([
      'tenant_id' => $this->user->tenant_id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
    $course1 = factory(Course::class)->create([
      'instructor_id' => $instructor1,
      'tenant_id' => $this->user->tenant_id,
      'term_id' => $schoolTerm1->id,
      'student_grade_id' => $studentGrade->id,
    ]);
    $course2 = factory(Course::class)->create([
      'instructor_id' => $instructor2,
      'tenant_id' => $this->user->tenant_id,
      'term_id' => $schoolTerm1->id,
      'student_grade_id' => $studentGrade->id,
    ]);
    $course3 = factory(Course::class)->create([
      'instructor_id' => $instructor3,
      'tenant_id' => $this->user->tenant_id,
      'term_id' => $schoolTerm2->id,
      'student_grade_id' => $studentGrade->id,
    ]);
    $this->registerStudent($schoolTerm1, $student1, $course1);
    $this->registerStudent($schoolTerm1, $student2, $course2);
    $this->registerStudent($schoolTerm2, $student2, $course3);
    $this->assertEquals(2, $schoolTerm1->assigned_instructors_count);
    $this->assertEquals(1, $schoolTerm2->assigned_instructors_count);
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
