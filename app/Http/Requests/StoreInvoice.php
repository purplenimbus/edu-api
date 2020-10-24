<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoice extends FormRequest
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
			'id' => 'exists:invoices,id',
			'recipient_id' => 'exists:users,id',
			'line_items' => 'array|required',
			'line_items.*.amount' => 'integer|min:0|required',
			'line_items.*.description' => 'string|distinct|required|max:255',
			'line_items.*.quantity' => 'integer|min:1|required',
			'status_id' => 'exists:invoice_statuses,id',
		];
	}
}
