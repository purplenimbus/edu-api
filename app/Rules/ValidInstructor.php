<?php

namespace App\Rules;

use App\Instructor;
use Illuminate\Contracts\Validation\Rule;

class ValidInstructor implements Rule
{
  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    return !is_null(Instructor::find($value));
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return ':attribute is an invalid instructor';
  }
}
