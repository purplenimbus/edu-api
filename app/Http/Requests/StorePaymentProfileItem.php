<?php

namespace App\Http\Requests;

use App\PaymentProfileItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
      'type' => ['required', 'integer', Rule::in(array_values(PaymentProfileItem::Types))],
      'tenant_id' => 'integer|required|exists:tenants,id',
    ];
  }
}
