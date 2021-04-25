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

    return array_merge($paymentProfileValidation->rules(),
    [
      'student_grade_id' => 'integer|exists:student_grades,id',
      'id' => 'required|integer|exists:payment_profiles,id',
      'name' => 'string|max:255',
      'school_term_type_id' => 'integer|exists:school_term_types,id|nullable',
    ]);
  }

  public function validationData(){
    return array_merge($this->all(), $this->route()->parameters());
  }
}
