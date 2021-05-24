<?php

namespace App\Observers;

use App\Course;
use App\CourseStatus;
use App\SchoolTerm;
use Illuminate\Support\Arr;

class CourseObserver
{
  /**
   * Handle the course "created" event.
   *
   * @param  \App\Course  $course
   * @return void
   */
  public function created(Course $course)
  {
    if (request()->has('instructor_id') && $course->wasChanged('instructor_id')) {	
      $course->instructor->assignInstructor($course);	
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
      $course->code = $course->parse_course_code();	
    }	

    if (is_null($course->schema)) {	
      $course->schema = config('edu.default.course_schema');	
    }

    $courseStatus = CourseStatus::where('name', 'created')->first();

    if ($courseStatus) {
      $course->status_id = $courseStatus->id;
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
      $course->instructor->assignInstructor($course);	
    }

    $courseStatus = Arr::get($course, "status.name", null);

    if ($courseStatus == 'complete')
    {
      $courseStatus = CourseStatus::whereName('in progress')->first();

      $otherCourses = $course->where([	
        ['tenant_id', '=', $course->tenant->id],	
        ['status_id', '=', $courseStatus->id],	
      ]);	

      if ($otherCourses->count() == 0 && isset($course->tenant->current_term)){	
        $course->tenant->current_term->update([
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
