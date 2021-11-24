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
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Tenant $tenant)
  {
    $this->tenant = $tenant;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    if ($this->tenant->has_current_term) {
      $this->tenant->current_term
        ->enrolledStudents()
        ->each(function(Student $student) {
          $student->notify(new StudentGradeAvailable);
          if ($student->guardian) {
            $student->guardian->notify(new StudentGradeAvailable);
          }
        });
    }
  }
}
