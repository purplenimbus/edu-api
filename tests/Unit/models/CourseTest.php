<?php

namespace Tests\Unit\Models;

use App\Course;
use App\Instructor;
use App\NimbusEdu\Institution;
use App\Registration;
use App\SchoolTerm;
use App\Student;
use App\StudentGrade;
use App\Subject;
use App\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
  use RefreshDatabase;
  /**
   * A course belongs to a tenant
   *
   * @return void
   */
  public function testItBelongsToTenant()
  {
    $tenant = factory(Tenant::class)->create();
    $course = factory(Course::class)->create([
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals($tenant->id, $course->tenant->id);
  }

  /**
   * A course belongs to school term
   *
   * @return void
   */
  public function testItBelongsToSchoolTerm()
  {
    $tenant = factory(Tenant::class)->create();
    $schoolTerm = factory(SchoolTerm::class)->create();
    $course = factory(Course::class)->create([
      'term_id' => $schoolTerm->id,
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals($schoolTerm->id, $course->term->id);
  }

  /**
   * A course has student grade
   *
   * @return void
   */
  public function testItHasStudentGrade()
  {
    $tenant = factory(Tenant::class)->create();
    $studentGrade = StudentGrade::first();
    $course = factory(Course::class)->create([
      'student_grade_id' => $studentGrade->id,
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals($studentGrade->id, $course->grade->id);
  }

  /**
   * A course has an instructor
   *
   * @return void
   */
  public function testItHasAnInstructor()
  {
    $instructor = factory(Instructor::class)->create();
    $tenant = factory(Tenant::class)->create();
    $course = factory(Course::class)->create([
      'instructor_id' => $instructor->id,
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals($instructor->id, $course->instructor->id);
  }

  /**
   * A course has a subject
   *
   * @return void
   */
  public function testItHasASubject()
  {
    $subject = factory(Subject::class)->create();
    $tenant = factory(Tenant::class)->create();
    $course = factory(Course::class)->create([
      'subject_id' => $subject->id,
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals($subject->id, $course->subject->id);
  }

  /**
   * A course has many registrations
   *
   * @return void
   */
  public function testItHasManyRegistrations()
  {
    $tenant = factory(Tenant::class)->create();
    $course = factory(Course::class)->create([ 'tenant_id' => $tenant->id ]);
    $registration1 = factory(Registration::class)->create([ 'course_id' => $course->id, 'tenant_id' => $tenant->id ]);
    $registration2 = factory(Registration::class)->create([ 'course_id' => $course->id, 'tenant_id' => $tenant->id ]);

    $this->assertEquals($course->id, $registration1->course->id);
    $this->assertEquals($course->id, $registration2->course->id);
  }

  /**
   * A course has a status
   *
   * @return void
   */
  public function testItHasAStatus()
  {
    $course = factory(Course::class)->create();

    $this->assertEquals('created', $course->status);
  }

  /**
   * A course is scoped to a student grade
   *
   * @return void
   */
  public function testItHasAStudentGradeScope()
  {
    $tenant = factory(Tenant::class)->create();
    $studentGrade1 = StudentGrade::whereAlias('js 1')->first();
    $studentGrade2 = StudentGrade::whereAlias('js 2')->first();
    factory(Course::class)->create([
      'student_grade_id' => $studentGrade1->id,
      'tenant_id' => $tenant->id,
    ]);
    factory(Course::class)->create([
      'student_grade_id' => $studentGrade1->id,
      'tenant_id' => $tenant->id,
    ]);
    factory(Course::class)->create([
      'student_grade_id' => $studentGrade2->id,
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals(2, Course::ofStudentGrade($studentGrade1->id)->count());
    $this->assertEquals(1, Course::ofStudentGrade($studentGrade2->id)->count());
  }

  /**
   * A student has valid courses they can enroll in
   *
   * @return void
   */
  public function testItHasAValidCoursesScope()
  {
    // $this->seed();
    $tenant1 = factory(Tenant::class)->create();
    $tenant2 = factory(Tenant::class)->create();
    $studentGrade1 = StudentGrade::ofTenant($tenant1->id)->whereAlias('js 1')->first();
    $studentGrade2 = StudentGrade::ofTenant($tenant2->id)->whereAlias('js 1')->first();
    $student1 = factory(Student::class)->create([ 
      'meta' => [ 
        'student_grade_id' => $studentGrade1->id,
      ],
      'tenant_id' => $tenant1->id,
    ]);
    $student2 = factory(Student::class)->create([ 
      'meta' => [ 
        'student_grade_id' => $studentGrade2->id,
      ],
      'tenant_id' => $tenant2->id,
    ]);
    $course1 = factory(Course::class)->create([
      'student_grade_id' => $studentGrade1->id,
      'tenant_id' => $tenant1->id,
    ]);
    $course2 = factory(Course::class)->create([
      'student_grade_id' => $studentGrade2->id,
      'tenant_id' => $tenant2->id,
    ]);
    factory(Course::class)->create([
      'student_grade_id' => $studentGrade1->id,
      'tenant_id' => $tenant1->id,
    ]);
    factory(Course::class)->create([
      'student_grade_id' => $studentGrade2->id,
      'tenant_id' => $tenant2->id,
    ]);
    $this->registerStudent($student1, $course1);
    $this->registerStudent($student2, $course2);

    $this->assertEquals(1, Course::validCourses($student1)->count());
    $this->assertEquals(1, Course::validCourses($student2)->count());
  }

  /**
   * A course is scoped to a school term
   *
   * @return void
   */
  public function testItHasASchoolTermScope()
  {
    $tenant = factory(Tenant::class)->create();
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $studentGrade1 = StudentGrade::whereAlias('js 1')->first();
    $studentGrade2 = StudentGrade::whereAlias('js 2')->first();
    $schoolTerm = factory(SchoolTerm::class)->create([
      'tenant_id' => $tenant->id,
    ]);
    factory(Course::class)->create([
      'student_grade_id' => $studentGrade1->id,
      'term_id' => $tenant->current_term->id,
      'tenant_id' => $tenant->id,
    ]);
    factory(Course::class)->create([
      'student_grade_id' => $studentGrade1->id,
      'term_id' => $schoolTerm->id,
      'tenant_id' => $tenant->id,
    ]);
    factory(Course::class)->create([
      'student_grade_id' => $studentGrade2->id,
      'term_id' => $tenant->current_term->id,
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals(2, Course::ofSchoolTerm($tenant->current_term->id)->count());
    $this->assertEquals(1, Course::ofSchoolTerm($schoolTerm->id)->count());
  }

  public function testItHasAnIncompleteScope()
  {
    $tenant = factory(Tenant::class)->create();
    factory(Course::class)->create([
      'status_id' => Course::Statuses['complete'],
      'tenant_id' => $tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      'status_id' => Course::Statuses['created'],
      'tenant_id' => $tenant->id,
    ]);
    $course3 = factory(Course::class)->create([
      'status_id' => Course::Statuses['in progress'],
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals(Course::incomplete()->get()->pluck(['id'])->toArray(), [$course2->id, $course3->id]);
  }

  private function registerStudent(Student $student, Course $course) {
    return factory(Registration::class)->create([
      'course_id' => $course->id,
      'user_id' => $student->id,
      'tenant_id' => $student->tenant->id,
    ]);
  }
}
