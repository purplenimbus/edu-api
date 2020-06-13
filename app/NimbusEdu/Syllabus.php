<?php

namespace App\Nimbus;

use App\Tenant;
use App\CourseGrade;
use App\Curriculum;
use App\CurriculumType;
use App\CurriculumCourseLoadType;
use App\Subject;
use NimbusEdu;

class Syllabus
{
  public $tenant;
  private $nimbus_edu;
  public $curriculum_type;
  public $processed;

  public function __construct(Tenant $tenant)
  {
    $this->tenant = $tenant;
    $this->nimbus_edu = new NimbusEdu($this->tenant);
    $this->curriculum_type = $this->getCurriculumType();
    $this->processed = [];
  }

  public function processCurriculum($data, $payload){
    try{
      $course_load = [
        'core' => [],
        'optional' => [],
        'elective' => [],
      ];

      foreach ($data as $course_load) {
      	$course_grade_id = $course_load->course_grade_id;

      	if ($course_grade_id) {
		      $query = [
      			'course_grade_id' => $course_grade_id,
      			'type_id' => $this->curriculum_type->id,
      		];

		      $curriculum = Curriculum::firstOrCreate($query);

	      	if(isset($course_load->core_subjects_code)) {
		      	$this->processSubjects(
		      		$course_load->core_subjects_code,
		      		$course_grade_id,
		      		'core',
		      		$curriculum,
		      	);
		      }

		      if(isset($course_load->elective_subjects_code)) {
		      	$this->processSubjects(
		      		$course_load->core_subjects_code,
		      		$course_grade_id,
		      		'elective',
		      		$curriculum,
		      	);
		      }

		      if(isset($course_load->optional_subjects_code)) {
		      	$this->processSubjects(
		      		$course_load->core_subjects_code,
		      		$course_grade_id,
		      		'optional',
		      		$curriculum,
		      	);
		      }
      	}
      }

      return $this->processed;

    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  public function getCurriculumType($new = false){
    return $new ? 
    CurriculumType::firstOrCreate(['country' => $this->tenant->country]) : 
    CurriculumType::where(['country' => $this->tenant->country])->first();
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
    		$this->processed[] = $curriculum
    			->subjects()
    			->firstOrCreate([
	    			'subject_id' => $subject->id,
	    			'type_id' => $curriculum_course_load_type->id
	    		])
	    		->toArray();
    	}
    }
  }

  private function getCurriculumCourseLoadType($name){
    return CurriculumCourseLoadType::where('name', $name)
    	->first();
  }

  private function getSubject($code){
    return Subject::where('code', $code)
    	->first();
  }
}