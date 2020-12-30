<?php

namespace App\Nimbus;

use App\CourseGrade;
use App\Curriculum;
use App\Nimbus\Helpers\Curriculum\CurriculumHelpers;
use App\Nimbus\Helpers\Subject\SubjectHelpers;
use App\SchoolTerm;
use App\SchoolTermStatus;
use App\Subject;
use App\Tenant;
use Carbon\Carbon;
use Exception;

class Institution
{
  use CurriculumHelpers, SubjectHelpers;

  public function newSchoolTerm(Tenant $tenant, $options = []) {
    $status_id = SchoolTermStatus::whereName('in progress')->first()->id;

    $data = array_merge([
      'end_date' => Carbon::now()->addMonths(4),
      'name' => 'first term',
      'status_id' => $status_id,
      'start_date' => Carbon::now(),
      'tenant_id' => $tenant->id,
    ], $options);

    SchoolTerm::create($data);
  }

  public function generateSubjects() {
    foreach($this->readJson('subjects.json') as $subject) {
      Subject::firstOrCreate(array_only($subject, ['code']), array_except($subject, ['code']));
    }
  }

  public function generateClasses() {
    foreach($this->readJson('course_grades.json') as $courseGrade) {
      CourseGrade::firstOrCreate(array_only($courseGrade, ['name']), array_except($courseGrade, ['name']));
    }
  }

  public function generateCurriculum() {
    foreach($this->readJson('curricula.json') as $courseLoad) {
      $this->processCourseLoad($courseLoad);
    }
  }

  public function processCourseLoad(array $course_load): void {
    $course_grade_id = $course_load['course_grade_id'];

    if ($course_grade_id) {
      $curriculum = Curriculum::firstOrCreate([
        'course_grade_id' => $course_grade_id,
        'type_id' => $this->getCurriculumType()->id,
      ]);

      if(isset($course_load['core_subjects_code'])) {
        $this->processSubjects(
          $course_load['core_subjects_code'],
          $course_grade_id,
          'core',
          $curriculum
        );
      }

      if(isset($course_load['elective_subjects_code'])) {
        $this->processSubjects(
          $course_load['elective_subjects_code'],
          $course_grade_id,
          'elective',
          $curriculum
        );
      }

      if(isset($course_load['optional_subjects_code'])) {
        $this->processSubjects(
          $course_load['optional_subjects_code'],
          $course_grade_id,
          'optional',
          $curriculum
        );
      }
    }
  }

  private function processSubjects(
    $data,
    $course_grade_id,
    $type,
    Curriculum $curriculum){
    $core_subjects_codes = explode(',', $data);

    foreach ($core_subjects_codes as $code) {
      $subject = $this->getSubject($code);
      $curriculum_course_load_type = $this->getCurriculumCourseLoadType($type);

      if ($subject && $curriculum_course_load_type) {
        $curriculum->subjects()
          ->firstOrCreate([
            'subject_id' => $subject->id,
            'type_id' => $curriculum_course_load_type->id
          ])
          ->toArray();
      }
    }
  }

  private function readJson($path){
    try{
      return json_decode(file_get_contents($path),true);
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }
}
