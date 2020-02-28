<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenant extends FormRequest
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
      'password'  => 'string|required',
      'username'  => 'required|unique:tenants|max:255',
      'email' => 'required|email|unique:tenants,email',
      'name' => 'string|required',
      'address.street' => 'string|required_with:address.city,address.country,address.state',
      'address.city' => 'string|required_with:address.street',
      'address.country' => 'string|required_with:address.street',
      'address.state' => 'string|required_with:address.street',
    ];
  }
}
