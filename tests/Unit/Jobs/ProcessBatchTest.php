<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessBatch;
use App\StudentGrade;
use App\Tenant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\NimbusEdu\Helpers\StudentImport;
use App\Student;

class ProcessBatchTest extends TestCase
{
  use RefreshDatabase;

  public function testItBatchImportsStudents()
  {
    $tenant = factory(Tenant::class)->create();
    $data = [
      [
        'firstname' => 'joe',
        'lastname' => 'boy',
        'email' => 'jobboy@yopmail.com',
        'date_of_birth' => Carbon::now()->toString(),
        'student_grade_id' => StudentGrade::first()->id,
      ],
      [
        'firstname' => 'jane',
        'lastname' => 'girl',
        'email' => 'janegirl@yopmail.com',
        'date_of_birth' => Carbon::now()->toString(),
        'student_grade_id' => StudentGrade::first()->id,
      ]
    ];

    $batch = new ProcessBatch($tenant, $data, "student");

    $batch->handle();

    $this->assertEquals(2, Student::count());
    $this->assertEquals([
      'jobboy@yopmail.com',
      'janegirl@yopmail.com'
    ], Student::all()->pluck('email')->toArray());
  }
}
