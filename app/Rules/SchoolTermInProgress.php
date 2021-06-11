<?php

namespace App\Rules;

use App\SchoolTerm;
use Illuminate\Contracts\Validation\Rule;

class SchoolTermInProgress implements Rule
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
    $schoolTerm = SchoolTerm::whereStatusId(SchoolTerm::Statuses['in progress'])->first();

    return is_null($schoolTerm);
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return __('validation.custom.school_term.already_in_progress');
  }
}
