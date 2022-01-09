<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidInstructor;

class AssignInstructor extends FormRequest
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
      'instructor_id' => [
        'required',
        'exists:users,id',
        new ValidInstructor()
      ],
      'course_id' => 'required|exists:courses,id'
    ];
  }
}
