<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentProfileItem extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return false;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'amount' => 'integer|required|nullable|min:1',
      'description' => 'string|required|distinct|max:255',
      'payment_profile_id' => 'integer|required|exists:payment_profiles,id',
      'type_id' => 'integer|required|exists:payment_profile_item_types,id',
      'tenant_id' => 'integer|required|exists:tenants,id',
    ];
  }
}
