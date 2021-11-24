<?php

namespace Tests\Unit\Jobs;

use App\Course;
use App\Jobs\CompleteTerm;
use App\Jobs\SendStudentGrades;
use App\NimbusEdu\Institution;
use App\Registration;
use App\SchoolTerm;
use App\Student;
use App\StudentGrade;
use App\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use OtherSeeders;
use SubjectsSeeder;
use Tests\TestCase;

class CompleteTermTest extends TestCase
{
  use RefreshDatabase;

  public function testItUpdatesAllOtherCourses()
  {
    $this->seed(OtherSeeders::class);
    $this->seed(SubjectsSeeder::class);
    $tenant = factory(Tenant::class)->create();
    $course1 = factory(Course::class)->create([
      'tenant_id' => $tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      'tenant_id' => $tenant->id,
    ]);
    $course3 = factory(Course::class)->create([
      'tenant_id' => $tenant->id,
    ]);
    $studentGrade = StudentGrade::first();
    $student1 = factory(Student::class)->create([
      'tenant_id' => $tenant->id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
    $student2 = factory(Student::class)->create([
      'tenant_id' => $tenant->id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $this->registerStudent($tenant->current_term, $student1, $course1);
    $this->registerStudent($tenant->current_term, $student1, $course3);
    $this->registerStudent($tenant->current_term, $student2, $course2);
  
    CompleteTerm::dispatchNow($tenant);

    $this->assertEquals(Course::Statuses['complete'], $course1->refresh()->status_id);
    $this->assertEquals(Course::Statuses['complete'], $course2->refresh()->status_id);
    $this->assertEquals(Course::Statuses['complete'], $course3->refresh()->status_id);
  }

  public function testItSendsStudentGrades()
  {
    Bus::fake();
    $this->seed(OtherSeeders::class);
    $this->seed(SubjectsSeeder::class);
    $tenant = factory(Tenant::class)->create();
    $course1 = factory(Course::class)->create([
      'tenant_id' => $tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      'tenant_id' => $tenant->id,
    ]);
    $course3 = factory(Course::class)->create([
      'tenant_id' => $tenant->id,
    ]);
    $studentGrade = StudentGrade::first();
    $student1 = factory(Student::class)->create([
      'tenant_id' => $tenant->id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
    $student2 = factory(Student::class)->create([
      'tenant_id' => $tenant->id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $this->registerStudent($tenant->current_term, $student1, $course1);
    $this->registerStudent($tenant->current_term, $student1, $course3);
    $this->registerStudent($tenant->current_term, $student2, $course2);

    $job = new CompleteTerm($tenant);
    $job->handle();

    Bus::assertDispatched(SendStudentGrades::class);
  }

  private function registerStudent(SchoolTerm $schoolTerm, Student $student, Course $course) {
    return factory(Registration::class)->create([
      'course_id' => $course->id,
      'tenant_id' => $schoolTerm->tenant_id,
      'term_id' => $schoolTerm->id,
      'user_id' => $student->id,
    ]);
  }
}