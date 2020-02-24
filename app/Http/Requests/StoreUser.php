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
      'email'  => 'email|unique:users,email',
      'firstname'   => 'required|string|max:255',
      'lastname'  => 'required|string|max:255',
      'othernames'  => 'string|string|max:255',
    ];
  }
}
