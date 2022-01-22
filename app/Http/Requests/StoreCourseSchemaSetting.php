<?php

namespace App\Http\Requests;

use App\Rules\ValidCourseSchema;
use App\Rules\ValidScores;
use Illuminate\Foundation\Http\FormRequest;

class StoreCourseSchemaSetting extends FormRequest
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
      "value" => [
        "required",
        "array",
        new ValidCourseSchema(),
      ],
      "value.*.name" => "required|string|max:255|distinct",
      "value.*.score" => [
        "required",
        "integer",
        "max:100",
      ],
    ];
  }
}
