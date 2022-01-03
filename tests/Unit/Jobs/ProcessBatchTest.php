<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessBatch;
use App\StudentGrade;
use App\Tenant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\NimbusEdu\Helpers\StudentImport;

class ProcessBatchTest extends TestCase
{
  use RefreshDatabase;

  public function testItBatchProcessesUsers()
  {
    $mock = $this->getMockForTrait(StudentImport::class);
    $mock->expects($this->any())
      ->method('importStudent')
      ->will($this->returnValue(TRUE));
  
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
  }
}