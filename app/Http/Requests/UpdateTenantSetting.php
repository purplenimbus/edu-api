<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantSetting extends FormRequest
{
  /**
   * Validation classes for tenant settings
   *
   * @return bool
   */
  const tenant_settings = [
    'course_schema' => StoreCourseSchemaSetting::class,
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
    $validation = new GetTenantSettings();

    if ($this->input("name") && isset(self::tenant_settings[$this->input("name")])) {
      $setting_validation = self::tenant_settings[$this->input("name")];

      $setting_validation = new $setting_validation();

      return array_merge(
        $validation->rules(),
        $setting_validation->rules(),
        [
          "name" => Rule::in(array_keys(self::tenant_settings))
        ]
      );
    }

    return array_merge(
      $validation->rules(),
      [
        "name" => Rule::in(array_keys(self::tenant_settings))
      ]
    );
  }

  public function validationData() {
    return array_merge($this->all(), $this->route()->parameters());
  }
}
