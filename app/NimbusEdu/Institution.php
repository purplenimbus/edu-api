<?php

namespace App\NimbusEdu;

use App\Curriculum;
use App\NimbusEdu\Helpers\CurriculumHelper;
use App\NimbusEdu\Helpers\MapsStudentGradeNumber;
use App\NimbusEdu\Helpers\SchoolTermHelper;
use App\NimbusEdu\Helpers\SubjectHelper;
use App\SchoolTerm;
use App\SchoolTermType;
use App\Subject;
use App\Tenant;
use Exception;

class Institution
{
  use CurriculumHelper, SubjectHelper, SchoolTermHelper, MapsStudentGradeNumber;

  public function newSchoolTerm(Tenant $tenant, string $termName, array $options = []) {
    $typeId = SchoolTermType::whereName($termName)->first()->id;

    $data = array_merge([
      'current_term' => true,
      'end_date' => $this->getSchoolTerm($termName)["end_date"],
      'name' => $termName,
      'status_id' => SchoolTerm::Statuses['in progress'],
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

  public function generateCurriculum(Tenant $tenant) {
    foreach($this->readJson('curricula.json') as $courseLoad) {
      $this->processCourseLoad($courseLoad, $tenant);
    }
  }

  private function processCourseLoad(array $course_load, Tenant $tenant): void {
    $studentGrade = $this->mapStudentGradeIndexToStudentGradeId($course_load['student_grade_id'], $tenant);

    if (isset($studentGrade->id)) {
      $curriculum = Curriculum::firstOrCreate([
        'tenant_id' => $tenant->id,
        'student_grade_id' => $studentGrade->id,
        'type_id' => $this->getCurriculumTypeId(Tenant::first()->country),
      ]);

      if(isset($course_load['core_subjects_code'])) {
        $this->processSubjects(
          $course_load['core_subjects_code'],
          'core',
          $curriculum
        );
      }

      if(isset($course_load['elective_subjects_code'])) {
        $this->processSubjects(
          $course_load['elective_subjects_code'],
          'elective',
          $curriculum
        );
      }

      if(isset($course_load['optional_subjects_code'])) {
        $this->processSubjects(
          $course_load['optional_subjects_code'],
          'optional',
          $curriculum
        );
      }
    }
  }

  private function processSubjects(
    $data,
    string $type,
    Curriculum $curriculum){
    $core_subjects_codes = explode(',', $data);

    foreach ($core_subjects_codes as $code) {
      $subject = $this->getSubject($code);
      $curriculum_course_load_type_id = $this->getCurriculumCourseLoadTypeId($type);

      if ($subject && isset($curriculum_course_load_type_id)) {
        $curriculum->subjects()
          ->firstOrCreate([
            'subject_id' => $subject->id,
            'type_id' => $curriculum_course_load_type_id
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
