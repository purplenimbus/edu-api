<?php

namespace App\NimbusEdu\Helpers;

use App\StudentGrade;
use App\Tenant;

trait MapsStudentGradeNumber
{
  public function mapStudentGradeIndexToStudentGradeId(int $number, Tenant $tenant)
  {
    $number--; //index starts from 0

    $alias = config("edu.default.student_grades.{$number}");

    if (!isset($alias)) {
      return;
    }

    return StudentGrade::ofTenant($tenant->id)->whereAlias($alias)->first();
  }
}
