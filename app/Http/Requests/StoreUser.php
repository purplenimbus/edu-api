<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
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
      'id' => 'integer|required|exists:users,id'
      'firstname'   => 'required|max:255',
      'lastname'  => 'required|max:255',
      'tenant_id' => 'exists:tenants,id'
    ];
  }
}
