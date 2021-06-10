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

      if ($this->tenant->current_term) {
        $courseData["term_id"] = $this->tenant->current_term->id;
      }

      $course = Course::firstOrNew($courseData);

      if (is_null($course->id)) {
        $course->save();
        $this->payload['created'][] = $course->toArray();
      } else {
        $course->save();
        $this->payload['updated'][] = $course->toArray();
      }
    }

    return $this->payload;
  }
}
