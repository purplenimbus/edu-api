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
      'id' => 'exists:courses,id|integer',
      'course_grade_id' => 'exists:course_grades,id|integer',
      'instructor_id' => 'integer',
      'name' => 'string', 
    ];
  }
}
