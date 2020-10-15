<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use App\Http\Requests\StoreCourse; 
use App\Course;

class StoreBatch extends FormRequest
{
  const types = [
    [
      'type' => 'course',
      'model' => Course::class,
      'validation' => StoreCourse::class,
    ],
  ];
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
    return array_merge([
      'type'  => [
        'required',
        Rule::in(Arr::pluck(self::types, 'type'))
      ],
      'data'  => 'required|array',
    ], $this->getRule());
  }

  private function getRule(): array {
    $rules = [];

    $validation = Arr::first(self::types, function ($value) {
      return $value['type'] === request()->type;
    });

    if ($validation) {
      $validation = new $validation['validation']();
      $validation_rules = $validation->rules();

      foreach(array_keys($validation_rules) as $key) {
        $rules["data.*.{$key}"] = is_string($validation_rules[$key]) ? $validation_rules[$key]."|distinct" : $validation_rules[$key];
      }

      return $rules;
    }

    return [];
  }
}
