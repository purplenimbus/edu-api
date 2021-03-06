<?php

namespace App\Http\Requests;

use App\SchoolTerm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetTerm extends FormRequest
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
      'id' => 'integer|exists:school_terms,id',
      'status_id' => ['integer', Rule::in(array_values(SchoolTerm::Statuses))],
    ];
  }
}
