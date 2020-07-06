<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Student;
use App\CourseGrade;
use App\Http\Requests\RegisterStudent;

class ValidRegistration implements Rule
{
  public $course_grade;
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct($course_grade_id)
  {
    $this->course_grade = CourseGrade::find($course_grade_id);
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $student_id)
  {
    $student = Student::find($student_id);

    return $student && $student->grade && $student->grade['id'] == $this->course_grade->id;
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return ":attribute is not a {$this->course_grade->name} student";
  }
}
