<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Course as Course;
use App\Tenant as Tenant;
use App\Subject as Subject;

use App\Notifications\CoursesGenerated;
use Illuminate\Support\Facades\Auth;

class GenerateCourses implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  var $curricula;
  var $tenant;
  var $payload;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Tenant $tenant, $curricula)
  {
    $this->curricula = $curricula;
    $this->tenant = $tenant;
    $this->payload = [
      'updated' => [],
      'created' => [],
      'skipped' => []
    ];
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $self = $this;
    $count = 0;

    foreach ($this->curricula as $curriculum) {
      foreach ($curriculum->course_load as $key => $section) {
        if(sizeof($section)){
          foreach ($section as $subject_id) {
            if(is_int($subject_id)){
              $subject = Subject::find($subject_id);

              $data = [
                'subject_id' => $subject->id,
                'tenant_id' => $self->tenant->id,
                'name' => $subject->name,
                'code' => strtoupper($subject->code.'-'.str_replace(' ','-',$curriculum->grade->name)),
                'student_grade_id' => $curriculum->student_grade_id,
                'schema' => config('edu.default.course_schema')
              ];

              $course = Course::firstOrNew(array_only($data,['code','tenant_id','student_grade_id']));

              if($course->id){
                $self->payload['updated'][] = $course;
              }else{
                $self->payload['created'][] = $course;
              }

              $course->fill($data);

              $course->save();

            }
          }
        }
      }
    }

    $this->tenant->notify(new CoursesGenerated($this->payload));

    \Log::info('GenerateCourses : '.sizeof($this->payload['created']).' Created , '.sizeof($this->payload['updated']).' Updated GeneratedCourses for tenant_id: '.$this->tenant->id);
  }
}
