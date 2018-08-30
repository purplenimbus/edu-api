<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourse extends FormRequest
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
            'instructor_id' => 'required|max:255|exists:users,id',
            //'id' => 'required|max:255|exists:courses,id',
            'tenant_id' => 'required|max:255|exists:tenants,id',
            'name' => 'required|max:255',
        ];
    }
}
