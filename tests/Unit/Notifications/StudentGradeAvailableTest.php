<?php

namespace Tests\Unit\Notifications;

use App\Guardian;
use App\NimbusEdu\Institution;
use App\Notifications\StudentGradeAvailable;
use App\Student;
use App\StudentGrade;
use App\Tenant;
use Tests\TestCase;

class StudentGradeAvailableTest extends TestCase
{
  public function testItSendsAnEmailToAStudent()
  {
    $studentGrade = StudentGrade::first();
    $tenant = factory(Tenant::class)->create();
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
    $this->assertStringContainsString('/messages', $mailData['actionUrl']);
    $this->assertEquals('View Result', $mailData['actionText']);
  }

  public function testItSendsAnEmailToAGuardian()
  {
    $studentGrade = StudentGrade::first();
    $tenant = factory(Tenant::class)->create();
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
    $this->assertStringContainsString('/messages', $mailData['actionUrl']);
    $this->assertEquals('View Result', $mailData['actionText']);
  }
}
