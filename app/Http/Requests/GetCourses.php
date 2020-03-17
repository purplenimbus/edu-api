<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
      'course_grade_id' => 'integer|exists:course_grades,id',
      'instructor_id' => 'integer|exists:users,id',
      'name' => 'string',
      'status_id' => 'integer|exists:course_statuses,id',
    ];
  }
}
