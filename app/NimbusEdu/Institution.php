<?php

namespace App\Nimbus;

use App\Curriculum;
use App\Nimbus\Helpers\Curriculum\CurriculumHelpers;
use App\Nimbus\Helpers\SchoolTerm\SchoolTermHelper;
use App\Nimbus\Helpers\Subject\SubjectHelpers;
use App\SchoolTerm;
use App\SchoolTermStatus;
use App\SchoolTermType;
use App\Subject;
use App\Tenant;
use Carbon\Carbon;
use Exception;

class Institution
{
  use CurriculumHelpers, SubjectHelpers, SchoolTermHelper;

  public function newSchoolTerm(Tenant $tenant, $termName, $options = []) {
    $statusId = SchoolTermStatus::whereName('in progress')->first()->id;
    $typeId = SchoolTermType::whereName($termName)->first()->id;

    $data = array_merge([
      'end_date' => $this->getSchoolTerm($termName)["end_date"],
      'name' => $termName,
      'status_id' => $statusId,
      'start_date' => $this->getSchoolTerm($termName)["start_date"],
      'tenant_id' => $tenant->id,
      'type_id' => $typeId,
    ], $options);

    return SchoolTerm::create($data);
  }

  public function generateSubjects() {
    foreach($this->readJson('subjects.json') as $subject) {
      Subject::firstOrCreate(array_only($subject, ['code']), array_except($subject, ['code']));
    }
  }

  public function generateCurriculum() {
    foreach($this->readJson('curricula.json') as $courseLoad) {
      $this->processCourseLoad($courseLoad);
    }
  }

  public function processCourseLoad(array $course_load): void {
    $student_grade_id = $course_load['student_grade_id'];

    if ($student_grade_id) {
      $curriculum = Curriculum::firstOrCreate([
        'student_grade_id' => $student_grade_id,
        'type_id' => $this->getCurriculumType()->id,
      ]);

      if(isset($course_load['core_subjects_code'])) {
        $this->processSubjects(
          $course_load['core_subjects_code'],
          $student_grade_id,
          'core',
          $curriculum
        );
      }

      if(isset($course_load['elective_subjects_code'])) {
        $this->processSubjects(
          $course_load['elective_subjects_code'],
          $student_grade_id,
          'elective',
          $curriculum
        );
      }

      if(isset($course_load['optional_subjects_code'])) {
        $this->processSubjects(
          $course_load['optional_subjects_code'],
          $student_grade_id,
          'optional',
          $curriculum
        );
      }
    }
  }

  private function processSubjects(
    $data,
    $student_grade_id,
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
