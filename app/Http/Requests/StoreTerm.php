<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTerm extends FormRequest
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
            'description'=> 'nullable|string',
            'end_date'=> 'date',
            'name'=> 'nullable|string',
            'meta'=>'nullable',
            'start_date'=>'date',
            'status_id'=> 'integer|exists:school_term_statuses,id',
            'tenant_id'=>'integer|tenants,id'
        ];
    }
}
