<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstructor extends FormRequest
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
      'course_grade_id' => 'integer|exists:course_grades,id',
      'date_of_birth' => 'date',
      'email'  => 'email',
      'firstname'   => 'max:255',
      'id' => 'required|integer|exists:users,id',
      'lastname'  => 'max:255',
      'othernames'  => 'nullable|string|max:255',
      'ref_id' => 'integer|unique:users,ref_id',
      'address.street' => 'string|required_with:address.city,address.country,address.state',
      'address.city' => 'string|required_with:address.street',
      'address.country' => 'string|required_with:address.street',
      'address.state' => 'string|required_with:address.street',
    ];
  }
}
