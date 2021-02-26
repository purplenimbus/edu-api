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
    $tenant = Auth::user()->tenant;

    return [
      'description' => 'string|nullable|max:255',
      'course_grade_id' => [
        'nullable',
        'integer',
        'exists:course_grades,id',
        'unique:payment_profiles,course_grade_id',
      ],
      'name' => 'string|required|max:255',
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
