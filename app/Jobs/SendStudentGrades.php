<?php

namespace App\Jobs;

use App\Notifications\StudentGradeAvailable;
use App\SchoolTerm;
use App\Student;
use App\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendStudentGrades implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $tenant;
  private $term;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Tenant $tenant, SchoolTerm $term)
  {
    $this->tenant = $tenant;
    $this->term = $term;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $this->term
      ->enrolledStudents()
      ->each(function(Student $student) {
        $student->notify(new StudentGradeAvailable($this->term, $student));
        if ($student->guardian) {
          $student->guardian->notify(new StudentGradeAvailable($this->term, $student));
        }
      });
  }
}
