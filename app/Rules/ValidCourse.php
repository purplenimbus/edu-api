<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\StudentGrade;
use App\Course;

class ValidCourse implements Rule
{
	public $studentGrade;
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct($student_grade_id)
	{
		$this->studentGrade = StudentGrade::find($student_grade_id);
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $course_id)
	{
		$course = Course::find($course_id);

    return $course && $course->grade && $course->grade->id == $this->studentGrade->id;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return ":attribute is not a {$this->studentGrade->name} course";
	}
}
