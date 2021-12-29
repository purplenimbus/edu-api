<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Tenant;
use App\Course;
use App\Jobs\SendStudentGrades;
use App\SchoolTerm;

class CompleteTerm implements ShouldQueue
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
    $course_ids = $this->tenant
      ->current_term
      ->registrations()
      ->pluck('course_id')
      ->unique()->values()->all();

    Course::find($course_ids)->toQuery()->update(['status_id' => Course::Statuses['complete']]);

    SendStudentGrades::dispatch($this->tenant, $this->tenant->current_term);

    $this->tenant->current_term->update(['status_id' => SchoolTerm::Statuses['complete']]);
  }
}
