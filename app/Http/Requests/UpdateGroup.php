<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidStudent;

class UpdateGroup extends FormRequest
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
        'id' => 'integer|required',
        'member_ids' => 'array|required',
        'member_ids.*' => [
          'required',
          'integer',
          'unique:user_group_members,user_id',
          new ValidStudent(),
        ]
      ];
    }

    public function validationData() {
      return array_merge($this->all(), $this->route()->parameters());
    }
  }
