<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteGroupMember extends FormRequest
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
      'member_ids' => 'array|required',
      'member_ids.*' => [
        'required',
        'integer',
        'exists:user_group_members, id',
      ],
    ];
  }
}
