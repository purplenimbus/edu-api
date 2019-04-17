<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourse extends FormRequest
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
      'id' => 'integer|exists:courses,id'
      'instructor_id' => 'integer|max:255|exists:users,id',
      'tenant_id' => 'integer|required|max:255|exists:tenants,id',
      'name' => 'string|required|max:255'
    ];
  }
}
