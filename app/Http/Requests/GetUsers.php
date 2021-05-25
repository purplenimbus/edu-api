<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetUsers extends FormRequest
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
      'account_status_id' => ['integer', Rule::in(array_values(User::StatusTypes))],
      'student_grade_id' => 'exists:student_grades,id|integer',
      'user_type' => Rule::in([
        'admin',
        'other',
        'guardian',
        'student',
        'superadmin',
        'teacher',
      ]),
    ];
  }
}
