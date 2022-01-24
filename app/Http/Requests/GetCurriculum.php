<?php

namespace App\Http\Requests;

use App\CurriculumCourseLoad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetCurriculum extends FormRequest
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
      'student_grade_id' => 'required|exists:student_grades,id',
      'filter.type_id' => ['integer', Rule::in(array_values(CurriculumCourseLoad::Types))],
      'filter.type' => ['string', Rule::in(array_keys(CurriculumCourseLoad::Types))],
    ];
  }

  public function validationData() {
    return array_merge($this->all(), $this->route()->parameters());
  }
}
