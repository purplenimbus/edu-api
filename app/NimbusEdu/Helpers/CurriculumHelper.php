<?php

namespace App\NimbusEdu\Helpers;

use App\CurriculumCourseLoadType;
use App\CurriculumType;

trait CurriculumHelper
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
