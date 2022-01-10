<?php

namespace App\Observers;

use App\Course;
use App\Jobs\SendStudentGrades;
use App\NimbusEdu\Helpers\CourseHelper;
use App\SchoolTerm;
use Illuminate\Support\Arr;

class CourseObserver
{
  use CourseHelper;
  /**
   * Handle the course "created" event.
   *
   * @param  \App\Course  $course
   * @return void
   */
  public function created(Course $course)
  {
    if (request()->has('instructor_id') && $course->wasChanged('instructor_id')) {	
      $course->instructor->setCoursePermissions($course);	
    }
  }

  /**
   * Handle the course "creating" event.
   *
   * @param  \App\Course  $course
   * @return void
   */
  public function creating(Course $course)
  {
    if (is_null($course->name) && $course->subject) {	
      $course->name = $course->subject->name;	
    }	

    if (is_null($course->code)) {	
      $course->code = $this->parseCourseCode($course);	
    }	

    if (is_null($course->schema)) {	
      $course->schema = config('edu.default.course_schema');	
    }

    if (is_null($course->status_id)) {	
      $course->status_id = Course::Statuses['created'];
    }
  }

  /**
   * Handle the course "saved" event.
   *
   * @param  \App\Course  $course
   * @return void
   */
  public function saved(Course $course)
  {
    if (request()->has('instructor_id') && $course->wasChanged('instructor_id') && isset($course->instructor_id)) {	
      $course->instructor->setCoursePermissions($course);	
    }
  }

  /**
   * Handle the course "saved" event.
   *
   * @param  \App\Course  $course
   * @return void
   */
  public function updated(Course $course)
  {
    $courseStatus = Arr::get($course, "status", null);

    if ($courseStatus == 'complete')
    {
      $otherCourses = $course
        ->ofTenant($course->tenant->id)
        ->incomplete();

      if ($course->tenant->has_current_term && $otherCourses->count() == 0){
        SendStudentGrades::dispatch($course->tenant, $course->tenant->current_term);

        $course->tenant->current_term->update([
          'current_term' => false,
          'status_id'=> SchoolTerm::Statuses['complete'],
        ]);
      }	
    }
  }

  /**
   * Handle the course "deleting" event.
   *
   * @param  \App\Course  $course
   * @return void
   */
  public function deleting(Course $course)
  {
    if ($course->registrations()->count() > 0) {	
      $course->registrations()->delete();	
    }
  }
}
