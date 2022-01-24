<?php

namespace App\Http\Requests;

use App\Rules\ValidBankAccount;
use Illuminate\Foundation\Http\FormRequest;

class DeleteBankAccount extends FormRequest
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
			'bank_account_id' => ['exists:bank_accounts,id', new ValidBankAccount($this->id)],
			'id'  => 'integer|required|exists:tenants,id',
		];
	}

	public function validationData() {
		return array_merge($this->all(), $this->route()->parameters());
	}
}
