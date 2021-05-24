<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\SchoolTerm;
use Illuminate\Validation\Rule;

class UpdateTerm extends FormRequest
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
      'status_id'  => [
        'number',
        'required',
        Rule::in(array_values(SchoolTerm::Statuses))
      ],
    ];
  }
}
