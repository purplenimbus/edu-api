<?php

namespace App\Nimbus\Helpers\Subject;

use App\Curriculum;
use App\Subject;

trait SubjectHelpers
{
  public function getSubject($code){
    return Subject::where('code', $code)
      ->first();
  }
}
