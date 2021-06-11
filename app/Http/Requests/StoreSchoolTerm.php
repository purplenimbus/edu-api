<?php

namespace App\Http\Requests;

use App\Rules\SchoolTermInProgress;
use App\SchoolTerm;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolTerm extends FormRequest
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
      'description' => 'nullable|string',
      'end_date' => 'date',
      'name' => 'nullable|string',
      'meta' => 'nullable',
      'start_date' => 'date',
      'status_id' => ['integer', Rule::in(array_values(SchoolTerm::Statuses))],
      'tenant_id' => 'integer|exists:tenants,id',
      'type_id' => [new SchoolTermInProgress()],
    ];
  }
}
