<?php

namespace Tests\Unit\Jobs;

use App\Course;
use App\Guardian;
use App\Jobs\SendStudentGrades;
use App\NimbusEdu\Institution;
use App\Notifications\StudentGradeAvailable;
use App\Student;
use App\StudentGrade;
use App\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use OtherSeeders;
use SubjectsSeeder;
use Tests\Helpers\RegisterStudent;
use Tests\TestCase;

class SendStudentGradesTest extends TestCase
{
  use RefreshDatabase, RegisterStudent;

  public function testItSendsAStudentGradeAvailableNotificationsToEnrolledStudentsAndTheirGuardian()
  {
    Notification::fake();
    $this->seed(OtherSeeders::class);
    $this->seed(SubjectsSeeder::class);
    $tenant = factory(Tenant::class)->create();
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $course1 = factory(Course::class)->create([
      'tenant_id' => $tenant->id,
    ]);
    $guardian1 = factory(Guardian::class)->create([
      'tenant_id' => $tenant->id,
    ]);
    $guardian2 = factory(Guardian::class)->create([
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
    $student3 = factory(Student::class)->create([
      'tenant_id' => $tenant->id,
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
    ]);
    $guardian1->assignWards([$student1->id]);
    $guardian2->assignWards([$student2->id]);
    $guardian1->assignWards([$student3->id]);

    $this->registerStudent($tenant->current_term, $student1, $course1);
    $this->registerStudent($tenant->current_term, $student2, $course1);

    $job = new SendStudentGrades($tenant, $tenant->current_term);
    $job->handle();

    Notification::assertSentTo(
      [$student1], StudentGradeAvailable::class
    );
    Notification::assertSentTo(
      [$student2], StudentGradeAvailable::class
    );
    Notification::assertSentTo(
      [$student1->guardian], StudentGradeAvailable::class
    );
    Notification::assertSentTo(
      [$student2->guardian], StudentGradeAvailable::class
    );
    Notification::assertNotSentTo(
      [$student3], StudentGradeAvailable::class
    );
  }
}