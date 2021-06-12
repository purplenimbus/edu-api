<?php

namespace Tests\Unit;

use App\Course;
use App\Guardian;
use App\NimbusEdu\Institution;
use App\Registration;
use App\SchoolTerm;
use App\Student;
use App\StudentGrade;
use App\Tenant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
  use RefreshDatabase;
  /**
   * generate student id
   *
   * @return void
   */
  public function testGenerateStudentId()
  {
    $knownDate = Carbon::create(2001, 5, 21, 12);
    Carbon::setTestNow($knownDate);  
    $student = factory(Student::class)->create();

    $this->assertEquals("20010001", $student->generateStudentId());
  }

  /**
   * student has a guardian
   *
   * @return void
   */
  public function testStudentHasAGuardian()
  {
    $student = factory(Student::class)->create();
    $guardian = factory(Guardian::class)->create();
    $guardian->assignWards([$student->id]);

    $this->assertEquals($guardian->id, $student->guardian->id);
  }

  /**
   * student has a student id
   *
   * @return void
   */
  public function testStudentHasAGrade()
  {
    $tenant = factory(Student::class)->create();
    $studentGrade = StudentGrade::first();
    $student = factory(Student::class)->create([
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals($studentGrade->id, $student->grade['id']);
  }

  /**
   * student has transcripts
   *
   * @return void
   */
  public function testStudentHasTranscripts()
  {
    $tenant = factory(Tenant::class)->create();
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $studentGrade = StudentGrade::first();
    $student = factory(Student::class)->create([
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
      'tenant_id' => $tenant->id,
    ]);
    $this->enrollStudent($student);
    $this->enrollStudent($student);

    $this->assertEquals($student->transcripts->first()->name, "first term");
    $this->assertEquals($student->transcripts->first()->registrations->count(), 2);
  }

  /**
   * students are scoped to student grade
   *
   * @return void
   */
  public function testStudenGradeScope()
  {
    $tenant = factory(Tenant::class)->create();
    $studentGrade1 = StudentGrade::whereAlias('js 1')->first();
    $studentGrade2 = StudentGrade::whereAlias('js 2')->first();
    factory(Student::class)->create([
      'meta' => [
        'student_grade_id' => $studentGrade1->id,
      ],
      'tenant_id' => $tenant->id,
    ]);
    factory(Student::class)->create([
      'meta' => [
        'student_grade_id' => $studentGrade1->id,
      ],
      'tenant_id' => $tenant->id,
    ]);
    factory(Student::class)->create([
      'meta' => [
        'student_grade_id' => $studentGrade2->id,
      ],
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals(2, Student::ofStudentGrade($studentGrade1->id)->count());
    $this->assertEquals(1, Student::ofStudentGrade($studentGrade2->id)->count());
  }

  /**
   * students are scoped as unregistered
   *
   * @return void
   */
  public function testUnregisteredStudentsScope()
  {
    $tenant = factory(Tenant::class)->create();
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $studentGrade = StudentGrade::whereAlias('js 1')->first();
    $student1 = factory(Student::class)->create([
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
      'tenant_id' => $tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
      'tenant_id' => $tenant->id,
    ]);

    $registration1 = $this->enrollStudent($student1);
    $this->enrollStudent($student2);

    $this->assertEquals(1, Student::ofUnregistered($registration1->course_id)->count());
  }

  /**
   * students are scoped to a tenant
   *
   * @return void
   */
  public function testStudentsTenantScope()
  {
    $tenant1 = factory(Tenant::class)->create();
    $tenant2 = factory(Tenant::class)->create();
    factory(Student::class)->create([
      'tenant_id' => $tenant1->id,
    ]);
    factory(Student::class)->create([
      'tenant_id' => $tenant1->id,
    ]);
    factory(Student::class)->create([
      'tenant_id' => $tenant2->id,
    ]);

    $this->assertEquals(2, Student::ofTenant($tenant1->id)->count());
    $this->assertEquals(1, Student::ofTenant($tenant2->id)->count());
  }

  /**
   * students have many registration
   *
   * @return void
   */
  public function testStudentsRegistrations()
  {
    $tenant = factory(Tenant::class)->create();
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $student = factory(Student::class)->create([
      'tenant_id' => $tenant->id,
    ]);
    $this->enrollStudent($student);
    $this->enrollStudent($student);

    $this->assertEquals(2, $student->registrations->count());
  }

  private function enrollStudent(Student $student) {
    $tenant = $student->tenant;

    $course = factory(Course::class)->create([
      'student_grade_id' => $student->grade['id'],
      'term_id' => $tenant->current_term->id,
      'tenant_id' => $tenant->id,
    ]);
    return factory(Registration::class)->create([
      'course_id' => $course->id,
      'term_id' => $tenant->current_term->id,
      'tenant_id' => $tenant->id,
      'user_id' => $student->id,
    ]);
  }
}
