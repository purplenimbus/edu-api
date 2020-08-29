<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidCourseSchema;

class StoreCourse extends FormRequest
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
      'course_grade_id' => 'required|integer|exists:course_grades,id',
      'instructor_id' => 'integer|exists:users,id',
      'name' => 'nullable|string|max:255',
      'schema' => 'array',
      'schema.*.name' => 'required|string|max:255',
      'schema.*.score' => 'required|integer|max:100',
      'subject_id' => 'integer|exists:subjects,id',
      'schema' => new ValidCourseSchema(),
      'start_date' => 'date',
      'end_date' => 'date|after:start_date',
    ];
  }
}
