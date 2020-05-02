<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidRegistration;
use App\Rules\ValidStudent;

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
      'course_id' => 'required|integer|exists:courses,id',
      'student_ids' => 'required|array',
      'student_ids.*' => [
        'required','integer', 'exists:users,id',
        new ValidStudent(),
        new ValidRegistration($this->input('course_id'))
      ],
    ];
  }
}
