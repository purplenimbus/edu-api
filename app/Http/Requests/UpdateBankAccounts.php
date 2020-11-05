<?php

namespace App\Http\Requests;

use App\Rules\ValidBankAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateBankAccounts extends FormRequest
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
		$validation = new StoreBankAccount();

		return array_merge([
			'account_name' => 'required_with:account_number|string|max:100',
			'bank_name' => 'required_with:bank_code|string',
			'bank_account_id' => ['exists:bank_accounts,id', new ValidBankAccount($this->tenant_id)],
		], Arr::except($validation->rules(), ['account_name', 'bank_name']));
	}

	public function validationData() {
		return array_merge($this->all(), $this->route()->parameters());
	}
}
