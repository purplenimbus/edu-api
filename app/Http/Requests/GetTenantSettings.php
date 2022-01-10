<?php

namespace App\Http\Requests;

use App\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetTenantSettings extends FormRequest
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
      'id'  => 'integer|required|exists:tenants,id',
      'name'  => ['required', 'string', Rule::in(array_keys(config('model_settings.defaultSettings.tenants')))],
    ];
  }

  public function validationData() {
    return array_merge($this->all(), $this->route()->parameters());
  }
}
