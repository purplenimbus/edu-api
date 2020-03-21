<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Teacher;

class ValidTeacher implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct()
  {
    
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
    return !is_null(Teacher::find($value));
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return ':attribute is an invalid teacher';
  }
}
