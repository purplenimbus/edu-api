<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\CourseGrade;
use App\Course;

class ValidCourse implements Rule
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
	public function passes($attribute, $course_id)
	{
		$course = Course::find($course_id);

    return $course && $course->grade && $course->grade->id == $this->course_grade->id;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return ":attribute is not a {$this->course_grade->name} course";
	}
}
