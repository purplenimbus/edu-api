<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstructor extends FormRequest
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
    $validation = new UpdateUser();

    return array_merge([], $validation->rules());
  }

  public function validationData(){
    return array_merge($this->all(), $this->route()->parameters());
  }
}
