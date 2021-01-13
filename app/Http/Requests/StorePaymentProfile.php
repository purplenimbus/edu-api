<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
    return [
      'description' => 'string|nullable|max:255',
      'course_grade_id' => 'integer|exists:course_grades,id',
      'name' => 'string|required|max:255',
      'tenant_id' => 'integer|exists:tenants,id',
      'term_id' => [
        'integer|exists:school_terms,id',
        Rule::unique('school_terms')->where(function ($query) {
          return $query->where('account_id', 1);
        })
      ],
    ];
  }
}
