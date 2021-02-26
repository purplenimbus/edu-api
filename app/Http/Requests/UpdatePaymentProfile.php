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
    $paymentProfileValidation = new StorePaymentProfile();
    $paymentProfileItemValidation = new StorePaymentProfileItem();
    $paymentProfileItemValidationRules = $paymentProfileItemValidation->rules();

    return array_merge($paymentProfileValidation->rules(),
    [
      'course_grade_id' => 'integer|exists:course_grades,id',
      'id' => 'required|integer|exists:payment_profiles,id',
      'name' => 'string|max:255',
      'items' => 'array',
      'items.*.amount' => $paymentProfileItemValidationRules['amount'],
      'items.*.description' => $paymentProfileItemValidationRules['description'],
      'items.*.type_id' => $paymentProfileItemValidationRules['type_id'],
      'term_type' => 'integer|exists:school_term_types,id',
    ]);
  }

  public function validationData(){
    return array_merge($this->all(), $this->route()->parameters());
  }
}
