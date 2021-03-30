<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;;

class StorePaymentProfile extends FormRequest
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
    $paymentProfileItemValidation = new StorePaymentProfileItem();
    $paymentProfileItemValidationRules = $paymentProfileItemValidation->rules();

    return [
      'description' => 'string|nullable|max:255',
      'student_grade_id' => [
        'nullable',
        'integer',
        'exists:student_grades,id',
        'unique:payment_profiles,student_grade_id',
      ],
      'name' => 'string|required|max:255',
      'items' => 'array',
      'items.*.amount' => $paymentProfileItemValidationRules['amount'],
      'items.*.description' => $paymentProfileItemValidationRules['description'],
      'items.*.type_id' => $paymentProfileItemValidationRules['type_id'],
      'tenant_id' => 'integer|exists:tenants,id',
      'term_type_id' => [
        'nullable',
        'integer',
        'exists:school_term_types,id',
        'unique:payment_profiles,term_type_id',
      ]
    ];
  }
}
