<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Student;
use App\Course;
use App\Http\Requests\RegisterStudent;

class ValidRegistration implements Rule
{
  public $course;
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct($id)
  {
    $this->course = Course::find($id);
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $id)
  {
    $student = Student::find($id);

    return isset($student->meta->course_grade_id) && $student->meta->course_grade_id === $this->course->grade->id;
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return ":attribute is not allowed to register for {$this->course->code} because of their grade";
  }
}
