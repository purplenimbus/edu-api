<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidScores;
use App\Rules\ValidScore;

class UpdateScores extends FormRequest
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
      'id' => 'required|integer|exists:registrations,id',
      'scores' => ['required','array', new ValidScores()],
      'scores.*.name' => 'required|string|max:255',
      'scores.*.score' => [
        'required',
        'integer',
        'max:100',
        new ValidScore($this->input('id'))
      ],
    ];
  }
}
