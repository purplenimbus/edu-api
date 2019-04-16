<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetCourses extends FormRequest
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
            'id' => 'exists:courses,id|number',
            'course_grade_id' => 'exists:course_grade_id,id|number',
            'instructor_id' => 'number',
            'name' => 'string',
        ];
    }
}
