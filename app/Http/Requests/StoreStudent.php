<?php

namespace App\Http\Requests;

use App\Rules\ValidGuardian;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidStudent;

class StoreStudent extends FormRequest
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
      'account_status_id' => 'integer|exists:account_status,id',
      'student_grade_id' => 'required|integer|exists:student_grades,id',
      'date_of_birth' => 'required|date',
      'email'  => 'required|email|max:255|unique:users,email',
      'firstname'   => 'required|max:255',
      'guardian_id' => ['exists:users,id', new ValidGuardian()],
      'id'   => ['exists:users,id', new ValidStudent()],
      'lastname'  => 'required|max:255',
      'othernames'  => 'nullable|string|max:255',
      'ref_id' => 'integer|unique:users,ref_id',
    ];
  }
}
