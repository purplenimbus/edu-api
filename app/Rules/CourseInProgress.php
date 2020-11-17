<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Course;

class CourseInProgress implements Rule
{
  /**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
    $course_in_progress = Course::find($value);
    
    return $course_in_progress->status->name == 'in progress';
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return __('validation.custom.course.not_in_progress');
	}
}
