<?php

namespace App\Nimbus;

use App\CourseGrade;
use App\Curriculum;
use App\CurriculumCourseLoadType;
use App\Tenant as Tenant;
use App\SchoolTerm as SchoolTerm;
use Carbon\Carbon;
use App\Jobs\ProcessBatch;
use App\Subject;
use Exception;

class Institution extends NimbusEdu
{
  var $tenant;

  public function __construct(Tenant $tenant)
  {
    $this->tenant = $tenant;

    $school_term =  SchoolTerm::firstOrcreate([
      'end_date' => Carbon::now()->addMonths(4),
      'name' => 'first term',
      'start_date' => Carbon::now(),
      'tenant_id' => $this->tenant->id,
    ]); // need to set this some how , perhaps pass it in the request?
  }

  public static function generateSubjects() {
    foreach(self::readJson('subjects.json') as $subject) {
      Subject::firstOrCreate(array_only($subject, ['code']), array_except($subject, ['code']));
    }
  }

  public static function generateClasses() {
    foreach(self::readJson('course_grades.json') as $courseGrade) {
      CourseGrade::firstOrCreate(array_only($courseGrade, ['name']), array_except($courseGrade, ['name']));
    }
  }

  public static function generateCurriculum() {
    foreach(self::readJson('curricula.json') as $curriculum) {
      dd($curriculum);

      $course_grade_id = $curriculum['course_grade_id'];

      if ($course_grade_id) {
        $curriculum = Curriculum::firstOrCreate([
          'course_grade_id' => $course_grade_id,
          'type_id' => $this->curriculum_type->id,
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
  }

  public function getCurriculumCourseLoadType($name){
    return CurriculumCourseLoadType::where('name', $name)
      ->first();
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
        $this->payload['created'][] = $curriculum
          ->subjects()
          ->firstOrCreate([
            'subject_id' => $subject->id,
            'type_id' => $curriculum_course_load_type->id
          ])
          ->toArray();
      }
    }

    return $this->payload;
  }

  private function getSubject($code){
    return Subject::where('code', $code)
      ->first();
  }

  private static function readJson($path){
    try{
      return json_decode(file_get_contents($path),true);
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }
}
