<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidStudent;

class GetTranscript extends FormRequest
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
      'id' => [
        'required',
        'exists:users,id',
        new ValidStudent()
      ],
      'term_id' => 'nullable|integer|exists:school_terms,id'
    ];
  }

  public function validationData(){
    return array_merge($this->all(), $this->route()->parameters());
  }
}
