<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use App\Http\Requests\StoreCourse; 
use App\Course;
use App\Guardian;
use App\Instructor;

class StoreBatch extends FormRequest
{
  const types = [
    [
      "type" => "course",
      "model" => Course::class,
      "validation" => StoreCourse::class,
    ],
    [
      "type" => "student",
      "model" => Student::class,
      "validation" => StoreStudent::class,
    ],
    [
      "type" => "instructor",
      "model" => Instructor::class,
      "validation" => StoreInstructor::class,
    ],
    [
      "type" => "guardian",
      "model" => Guardian::class,
      "validation" => StoreGuardian::class,
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
      "type"  => [
        "required",
        Rule::in(Arr::pluck(self::types, "type"))
      ],
      "data"  => "required|array",
    ], $this->getRule());
  }

  private function getRule(): array {
    $rules = [];

    $validation = Arr::first(self::types, function ($value) {
      return $value["type"] === request()->type;
    });

    if ($validation) {
      $validation = new $validation["validation"]();
      $validation_rules = $validation->rules();

      foreach(array_keys($validation_rules) as $key) {
        $rules["data.*.{$key}"] = $this->getValidationKey($validation_rules, $key);
      }

      return $rules;
    }

    return [];
  }

  private function getValidationKey (array $validation_rules, string $key) {
    return is_string($validation_rules[$key]) && ($key === "email" || $key === "ref_id") ? $validation_rules[$key]."|distinct" : $validation_rules[$key];
  }
}
