<?php

namespace App\Http\Requests;

use App\Rules\ValidInstructor;
use Illuminate\Foundation\Http\FormRequest;

class GetInstructor extends FormRequest
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
      'id' => ['exists:users,id', new ValidInstructor()]
    ];
  }

  public function validationData()
  {
    return array_merge($this->all(), $this->route()->parameters());
  }
}
