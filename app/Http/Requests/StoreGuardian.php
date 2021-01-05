<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\StoreUser;
use App\Rules\ValidStudent;

class StoreGuardian extends FormRequest
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
    $userValidation = new StoreUser();

    return array_merge([
      'ward_ids'  => [
        'array',
      ],
      'ward_ids.*'  => ['required_with:ward_ids', 'integer', new ValidStudent()],
    ], $userValidation->rules());
  }
}
