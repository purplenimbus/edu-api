<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCourseSchema implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
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
    return array_reduce($value, array($this, "sum_schema")) === 100;
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return 'The sum of the course scores must be 100';
  }

  private function sum_schema($carry, $item)
  {
    $carry += $item["score"];
    return $carry;
  }
}
