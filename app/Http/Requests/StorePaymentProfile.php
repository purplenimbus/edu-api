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
      'flat_fee' => 'boolean',
      'student_grade_id' => [
        'nullable',
        'integer',
        'exists:student_grades,id',
        Rule::unique('payment_profiles')->where(function ($query) {
          return $query->where('student_grade_id', request()->student_grade_id)
            ->where('school_term_type_id', request()->school_term_type_id);
        })
      ],
      'name' => 'string|required|max:255',
      'items' => 'array',
      'items.*.amount' => $paymentProfileItemValidationRules['amount'],
      'items.*.description' => $paymentProfileItemValidationRules['description'],
      'items.*.type' => $paymentProfileItemValidationRules['type'],
      'tenant_id' => 'integer|exists:tenants,id',
      'school_term_type_id' => [
        'nullable',
        'integer',
        'exists:school_term_types,id',
        'unique:payment_profiles,school_term_type_id',
        'required_if:flat_fee,null'
      ]
    ];
  }
}
