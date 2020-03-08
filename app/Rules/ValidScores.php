<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Registration;
use Illuminate\Support\Arr;

class ValidScores implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct()
  {}

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    $total = array_reduce(Arr::pluck($value, 'score'), function($total, $value) {
      return $total+$value;
    });

    return $total <= 100;
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return ":attribute may not be greater than 100";
  }
}
