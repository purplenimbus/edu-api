<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentProfile extends FormRequest
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
    $validation = new StorePaymentProfile();

    return array_merge([
      'id' => 'required|integer|exists:payment_profiles,id',
    ], $validation->rules());
  }

  public function validationData(){
    return array_merge($this->all(), $this->route()->parameters());
  }
}
