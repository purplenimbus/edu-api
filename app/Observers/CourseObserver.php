<?php

namespace App\Observers;

use App\Course;

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
    $this->setDefaultAttributes($course);

    $this->setNewInstructor($course);
  }

  /**
   * Handle the course "updated" event.
   *
   * @param  \App\Course  $course
   * @return void
   */
  public function updated(Course $course)
  {
    $this->updateCourseAttributes($course);
  }

  /**
   * Handle the course "deleted" event.
   *
   * @param  \App\Course  $course
   * @return void
   */
  public function deleted(Course $course)
  {
    $this->deleteRegisteredStudents($course);
  }

  private function setDefaultAttributes(Course $course)
  {
    if (is_null($course->name)) {
      $course->name = $course->subject->name;
    }

    if (is_null($course->code)) {
      $course->code = $course->parse_course_code();
    }

    if (is_null($course->schema)) {
      $course->schema = config('edu.default.course_schema');
    }
  }

  private function setNewInstructor(Course $course)
  {
    if (request()->has('instructor_id') && $course->wasChanged('instructor_id')) {
      $course->instructor->assignInstructor($course);
    };
  }

  private function updateCourseAttributes(Course $course)
  {
    if (request()->has('instructor_id') && $course->wasChanged('instructor_id') && isset($course->instructor_id)) {
      $course->instructor->assignInstructor($course);
    }

    $this->updateCourseStatus($course);
  }

  private function updateCourseStatus(Course $course)
  {
    if ($course->status->name == 'complete') {
      $courses_in_progres = $course->where([
        ['tenant_id', '=', $course->tenant->id],
        ['status_id', '=', 1],
      ]);

      if ($courses_in_progres->count() == 0 && isset($course->tenant->current_term)) {
        $course->tenant->current_term->update(['status_id' => 2]);
      }
    }
  }

  private function deleteRegisteredStudents(Course $course)
  {
    if ($course->registrations()->count() > 0) {
      $course->registrations()->delete();
    }
  }
}
