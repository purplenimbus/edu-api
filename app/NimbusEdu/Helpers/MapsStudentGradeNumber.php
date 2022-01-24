<?php

namespace App\NimbusEdu\Helpers;

use App\StudentGrade;
use App\Tenant;

trait MapsStudentGradeNumber
{
  public function mapStudentGradeIndexToStudentGradeId(int $number, Tenant $tenant)
  {
    $studentGrade = config("edu.default.student_grades.{$number}");

    if (!isset($studentGrade["alias"])) {
      return;
    }

    return StudentGrade::ofTenant($tenant->id)->whereAlias($studentGrade["alias"])->first();
  }
}
