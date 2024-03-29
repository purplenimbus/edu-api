<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends FormRequest
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
    $validation = new StoreUser();
  
    return array_merge($validation->rules(), [
      'email'  => 'email',
      'firstname'   => 'string|max:255',
      'lastname'  => 'string|max:255',
      'id' => 'required|integer|exists:users,id',
      'tenant_id' => 'integer|exists:tenants,id',
    ]);
  }

  public function validationData() {
    return array_merge($this->all(), $this->route()->parameters());
  }
}
