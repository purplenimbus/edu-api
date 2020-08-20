<?php

namespace App\Rules;

use App\BankAccount;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;

class ValidBankAccount implements Rule
{
	var $tenant_id;
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct($tenant_id)
	{
		$this->tenant_id = $tenant_id;
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		$bank_account = BankAccount::find($value);
		
		return isset($bank_account->tenant) && $bank_account->tenant->id == $this->tenant_id;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return __('validation.custom.bank_account.dosent_belong');
	}
}
