<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
      'date_of_birth' => 'date',
      'email'  => 'email|unique:users,email',
      'firstname'   => 'required|string|max:255',
      'lastname'  => 'required|string|max:255',
      'othernames'  => 'nullable|string|max:255',
      'account_status_id' => 'integer|exists:account_status,id',
      'address.street' => 'string|required_with:address.city,address.country,address.state',
      'address.city' => 'string|required_with:address.street',
      'address.country' => 'string|required_with:address.street',
      'address.state' => 'string|required_with:address.street',
      'address.phone_number' => 'string',
    ];
  }
}
