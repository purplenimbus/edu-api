<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Registration;

class ValidScore implements Rule
{
  public $registration;
  public $score;
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct($id)
  {
    $this->registration = Registration::find($id);
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
    if (isset($this->registration->course->schema[$this->get_index($attribute)])
      &&
      isset($this->registration->course->schema[$this->get_index($attribute)]['score'])) {
      $this->score = $this->registration->course->schema[$this->get_index($attribute)]['score'];

      return $this->score &&
      $this->registration->course->schema[$this->get_index($attribute)]['score'] >= $value;
    }

    return false;
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return ":attribute may not be greater than {$this->score}";
  }

  private function get_index($attribute){
    return intval(explode('.', $attribute)[1]);
  }
}
