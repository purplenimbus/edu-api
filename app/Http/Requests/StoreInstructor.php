<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstructor extends FormRequest
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
      'date_of_birth' => 'required|date',
      'email'  => 'required|email|max:255|unique:users,email',
      'firstname'   => 'required|max:255',
      'lastname'  => 'required|max:255',
      'othernames'  => 'string|max:255',
      'ref_id' => 'integer|unique:users,ref_id',
      'tenant_id' => 'required|integer|exists:tenants,id',
    ];
  }
}
