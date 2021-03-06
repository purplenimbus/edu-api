<?php

namespace App\Http\Requests;

use App\Course;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidCourseSchema;
use Illuminate\Validation\Rule;

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
      'student_grade_id' => 'integer|exists:student_grades,id',
      'id' => 'required|integer|exists:courses,id',
      'instructor_id' => 'integer|max:255|exists:users,id|nullable',
      'name' => 'string|max:255',
      'schema' => 'array',
      'schema.*.name' => 'required|string|max:255',
      'schema.*.score' => 'required|integer|max:100',
      'subject_id' => 'integer|exists:subjects,id',
      'schema' => new ValidCourseSchema(),
      'status_id' => ['integer', Rule::in(array_values(Course::Statuses))],
    ];
  }

  public function validationData(){
    return array_merge($this->all(), $this->route()->parameters());
  }
}
