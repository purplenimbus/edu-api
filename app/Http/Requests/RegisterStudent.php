<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidRegistration;
use App\Rules\ValidStudent;
use App\Rules\ValidCourse;
use App\Rules\CourseInProgress;

class RegisterStudent extends FormRequest
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
      'student_grade_id' => 'required|integer|exists:student_grades,id',
      'course_ids' => 'required|array|max:10',
      'course_ids.*' => [
        'required','integer','distinct','exists:courses,id',
        new ValidCourse($this->input('student_grade_id')),
        new CourseInProgress()
      ],
      'student_ids' => 'required|array|max:10',
      'student_ids.*' => [
        'required','integer','distinct','exists:users,id',
        new ValidStudent(),
        new ValidRegistration($this->input('student_grade_id'))
      ],
    ];
  }
}
