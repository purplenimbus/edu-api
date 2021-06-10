<?php

namespace App\NimbusEdu\Helpers;

use App\Subject;

trait SubjectHelper
{
  public function getSubject($code){
    return Subject::where('code', $code)
      ->first();
  }
}
