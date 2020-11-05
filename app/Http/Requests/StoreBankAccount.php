<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccount extends FormRequest
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
			'account_name' => 'required|required_with:account_number|string|max:100',
			'account_number' => 'required_with:account_name|digits:10',
			'bank_name' => 'required|required_with:bank_code|string',
			'bank_code' => 'required_with:bank_name|string',
			'description' => 'nullable|string',
			'default' => 'boolean',
			'tenant_id' => 'exists:tenants,id',
		];
	}

	public function validationData() {
		return array_merge($this->all(), $this->route()->parameters());
	}
}
