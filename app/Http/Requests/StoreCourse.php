<?php

namespace App\Http\Requests;

use App\Course;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidCourseSchema;
use Illuminate\Validation\Rule;

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
      'student_grade_id' => [
        'required',
        'integer',
        'exists:student_grades,id',
        // new UniqueCourse()
      ],
      'instructor_id' => 'integer|exists:users,id',
      'name' => 'nullable|string|max:255',
      'schema' => 'array',
      'schema.*.name' => 'required|string|max:255',
      'schema.*.score' => 'required|integer|max:100',
      'subject_id' => 'required|integer|exists:subjects,id',
      'schema' => new ValidCourseSchema(),
      'start_date' => 'date',
      'end_date' => 'date|after:start_date',
    ];
  }
}
