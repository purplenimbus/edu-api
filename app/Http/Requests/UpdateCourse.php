<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidCourseSchema;

class UpdateCourse extends FormRequest
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
      'course_grade_id' => 'integer|exists:course_grades,id',
      'id' => 'required|integer|exists:courses,id',
      'instructor_id' => 'integer|max:255|exists:users,id',
      'name' => 'string|max:255',
      'schema' => 'array',
      'schema.*.name' => 'required|string|max:255',
      'schema.*.score' => 'required|integer|max:100',
      'subject_id' => 'integer|exists:subjects,id',
      'schema' => new ValidCourseSchema(),
      'status_id' => 'integer|exists:course_statuses,id',
    ];
  }

  public function validationData(){
    return array_merge($this->all(), $this->route()->parameters());
  }
}
