<?php

namespace App\Nimbus;

use App\Tenant;
use App\Course;
use App\Curriculum;
use App\Nimbus\Helpers\Curriculum\CurriculumHelpers;
use App\Nimbus\Helpers\Subject\SubjectHelpers;

class Syllabus
{
  use SubjectHelpers, CurriculumHelpers;
  
  public $tenant;
  public $curriculum_type;
  public $payload = [
    'created' => [],
    'updated' => [],
  ];

  public function __construct(Tenant $tenant)
  {
    $this->tenant = $tenant;
  }

  public function processCourses(array $coursesData): array {
    foreach($coursesData as $courseData) {
      $courseData["tenant_id"] = $this->tenant->id;

      $course = Course::firstOrNew($courseData);

      if ($course->id) {
        $this->payload['updated'][] = $course;
      } else {
        $this->payload['created'][] = $course;
      }

      $course->save();
    }

    return $this->payload;
  }
}
