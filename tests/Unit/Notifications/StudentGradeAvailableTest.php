<?php

namespace Tests\Unit\Notifications;

use App\Guardian;
use App\NimbusEdu\Institution;
use App\Notifications\StudentGradeAvailable;
use App\Student;
use App\StudentGrade;
use App\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentGradeAvailableTest extends TestCase
{
  use RefreshDatabase;

  public function testItSendsAnEmailToAStudent()
  {
    $tenant = factory(Tenant::class)->create();
    $studentGrade = StudentGrade::first();
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $student = factory(Student::class)->create([
      'first_name' => 'joey',
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
      'tenant_id' => $tenant->id,
    ]);    
    $notification = new StudentGradeAvailable($tenant->current_term, $student);

    $mailData = $notification->toMail($student)->toArray();

    $this->assertEquals('Hi Joey!', $mailData['greeting']);
    $this->assertEquals('Your first term result is available', $mailData['subject']);
    $this->assertEquals('View Result', $mailData['actionText']);
  }

  public function testItSendsAnEmailToAGuardian()
  {
    $tenant = factory(Tenant::class)->create();
    $studentGrade = StudentGrade::first();
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $student = factory(Student::class)->create([
      'first_name' => 'joey',
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
      'tenant_id' => $tenant->id,
    ]);
    $guardian = factory(Guardian::class)->create([
      'first_name' => 'james',
    ]);    
    $notification = new StudentGradeAvailable($tenant->current_term, $student);

    $mailData = $notification->toMail($guardian)->toArray();

    $this->assertEquals('Hi James!', $mailData['greeting']);
    $this->assertEquals('Joey\'s first term result', $mailData['subject']);
    $this->assertContains('Joey\'s first term result has been posted and is available for viewing', $mailData['introLines']);
    $this->assertEquals('View Result', $mailData['actionText']);
  }

  public function testItSavesADatabaseNotificationForAGuardian()
  {
    $tenant = factory(Tenant::class)->create();
    $studentGrade = StudentGrade::first();
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $guardian = factory(Guardian::class)->create([
      'first_name' => 'james',
    ]); 
    $student = factory(Student::class)->create([
      'first_name' => 'joey',
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
      'tenant_id' => $tenant->id,
    ]);
    
    $notification = new StudentGradeAvailable($tenant->current_term, $student);

    $this->assertEquals([
      'message' => "Joey's first term result has been posted and is available for viewing"
    ], $notification->toArray($guardian));
  }

  public function testItSavesADatabaseNotificationForAStudent()
  {
    $tenant = factory(Tenant::class)->create();
    $studentGrade = StudentGrade::first();
    $institution = new Institution();
    $institution->newSchoolTerm($tenant, 'first term');
    $student = factory(Student::class)->create([
      'first_name' => 'joey',
      'meta' => [
        'student_grade_id' => $studentGrade->id,
      ],
      'tenant_id' => $tenant->id,
    ]);
    
    $notification = new StudentGradeAvailable($tenant->current_term, $student);

    $this->assertEquals([
      'message' => "Your first term result has been posted and is available for viewing"
    ], $notification->toArray($student));
  }
}
