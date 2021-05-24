<?php

namespace App\Http\Requests;

use App\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetCourses extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'id' => 'integer|exists:courses,id',
      'student_grade_id' => 'integer|exists:student_grades,id',
      'instructor_id' => 'integer|exists:users,id',
      'name' => 'string',
      'status_id' => ['integer', Rule::in(array_values(Course::Statuses))],
    ];
  }
}
