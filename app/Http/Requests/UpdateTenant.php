<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenant extends FormRequest
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
      'id'  => 'integer|required|exists:tenants,id',
      'name' => 'string',
      'logo' => 'mimes:jpeg,png|nullable|max:1048576',
      'address.street' => 'string|required_with:address.city,address.country,address.state',
      'address.city' => 'string|required_with:address.street',
      'address.country' => 'string|required_with:address.street',
      'address.state' => 'string|required_with:address.street',
    ];
  }

  public function validationData(){
    return array_merge($this->all(), $this->route()->parameters());
  }
}
