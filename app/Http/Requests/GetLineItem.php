<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetLineItem extends FormRequest
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
			'id' => 'integer|exists:line_items,id',
			'invoice_id' => 'integer|exists:invoices,id',
		];
	}

	public function validationData(){
    return array_merge($this->all(), $this->route()->parameters());
  }
}
