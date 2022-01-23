<?php

namespace App\NimbusEdu\Helpers;

use App\Curriculum;
use App\CurriculumCourseLoad;

trait CurriculumHelper
{
  public function getCurriculumTypeId(string $type): int {
    return Curriculum::Types[$type];
  }

  public function getCurriculumCourseLoadTypeId(string $name): int {
    return CurriculumCourseLoad::Types[$name];
  }
}
