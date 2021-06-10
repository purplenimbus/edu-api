<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\StoreCourse;
use Illuminate\Support\Arr;

class StoreBatchCourses extends FormRequest
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
		return array_merge([
      'data'  => 'required|array',
    ], $this->getRule());
  }

  private function getRule(): array {
    $rules = [];

		$validation = new StoreCourse();

		$validation_rules = $validation->rules();

		foreach(array_keys($validation_rules) as $key) {
			$rules["data.*.{$key}"] = $validation_rules[$key];
		}

		return $rules;
  }
}
