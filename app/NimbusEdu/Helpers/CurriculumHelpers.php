<?php

namespace App\Nimbus\Helpers\Curriculum;

use App\CurriculumCourseLoadType;
use App\CurriculumType;

trait CurriculumHelpers
{
  public function getCurriculumType($new = false, $country = null){
    $country = $country ?? config("edu.default.country");

    return $new ?
      CurriculumType::firstOrCreate(['country' => $country]) : 
      CurriculumType::where(['country' => $country])->first();
  }

  public function getCurriculumCourseLoadType($name){
    return CurriculumCourseLoadType::where('name', $name)
      ->first();
  }
}
