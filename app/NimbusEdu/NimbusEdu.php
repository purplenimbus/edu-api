<?php

namespace App\NimbusEdu;

use App\Tenant;
use App\User;
use App\Student;
use App\Instructor;
use App\Subject;
use App\Course;
use App\Curriculum;
use App\StudentGrade;
use App\Registration;
use App\Invoice;
use App\Guardian;
use App\NimbusEdu\Helpers\CourseHelper;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class NimbusEdu
{
  use CourseHelper;

  var $tenant;

  public function __construct(Tenant $tenant)
  {
    $this->tenant = $tenant;
  }

  public function processSubject($data, $payload){
    try{
      $subject = Subject::firstOrNew(array_only($data, ['code']));

      if($subject->id){
        $payload['updated'][] = $subject;
      }else{
        $payload['created'][] = $subject;
      }

      if(isset($data['tenant_id'])){
        unset($data['tenant_id']);
      }

      $subject->fill($data);

      $subject->save();

      return $payload;

    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  public function processStudentGrade($data, $payload){
    try{
      $curriculum = StudentGrade::firstOrNew(array_only($data, ['name']));

      if($curriculum->id){
        $payload['updated'][] = $curriculum;
      }else{

        $payload['created'][] = $curriculum;
      }

      if(isset($data['tenant_id'])){
        unset($data['tenant_id']);
      }

      $curriculum->fill($data);

      $curriculum->save();

      return $payload;
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  } 

  public function processResults($data, $payload){
    try{
      $registration = Registration::with(['user','course:id,code'])->findOrFail($data['id']);

      $registration->fill($data);

      $registration->save();

      $payload['updated'][] = $registration;

      $payload['resource'] = $registration->course;

      return $payload;

    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  public function enrollCoreCourses(Student $student, $student_grade_id){
    try{
      var_dump('Attempting to enroll student: '.$student->id);

      $school_term = $this->tenant->current_term;

      $invoice = Invoice::firstOrCreate([
        'tenant_id' => $this->tenant->id,
        'term_id' => $school_term->id
      ]);

      if ($this->getCourseLoadIds($student_grade_id)['core']) {
        foreach ($this->getCourseLoadIds($student_grade_id)['core'] as $course) {

          var_dump('Enrolling '.$student->ref_id.' in '.$course['code']);
  
          $registration = Registration::firstOrCreate([
            'tenant_id' => $this->tenant->id ,
            'user_id' => $student->id ,
            'course_id' => $course['id'],
            'term_id' => $school_term->id,
            'invoice_id' => $invoice->id
          ]);
  
          $student->account_status_id = User::StatusTypes['registered'];
  
          $student->save();
  
          \Log::info('Student '.$student->id.' Registered in '.$course['code'].' , Registration ID '.$registration->id);
  
          var_dump('Student '.$student->id.' Registered in '.$course['code'].' , Registration ID '.$registration->id);
        }
      }
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  private function getCourseLoadIds($student_grade_id){
    try{
      $curriculum = Curriculum::with('grade')->where('student_grade_id',$student_grade_id)->first();

      $course_load = [];

      if(isset($curriculum->course_load)){
        foreach ($curriculum->course_load as $key => $section) {
          $course_load[$key] = [];

          if(sizeof($section)){
            foreach ($section as $subject_id) {
              if(is_int($subject_id)){
                $subject = Subject::find($subject_id);

                $course = Course::where('code',$this->parse_course_code($subject->code, $curriculum->grade->name))->first();

                $course_load[$key][] = $course->only(['id','code']);
              }
            }
          }
        }

        return $course_load;
      }
    }catch(ModelNotFoundException $ex){      
     throw new Exception('No Curriculum found with grade id '+$student_grade_id);
    }
  }

  private function parse_course_code($subject_code, $grade_name){
    return strtoupper($subject_code.'-'.str_replace(' ','-',$grade_name));
  }

  private function parseSubjects($data, Curriculum $curriculum, $create_course = false){
    try{
      $parsed = [];

      $core_subjects_codes = explode(',',$data);

      foreach ($core_subjects_codes as $core_subject_code) {
        $core_subject = Subject::where('code',$core_subject_code)->first();

        if($create_course && isset($core_subject->id) && is_int($core_subject->id)){
          $parsed[] = $core_subject->id;

          $course = $this->create_course($core_subject, $curriculum); 
        }
      }

      unset($data);

      return $parsed;
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  private function create_course(Subject $subject, Curriculum $curriculum){
    try{
      $data = [
        'subject_id' => $subject->id,
        'tenant_id' => $this->tenant->id,
        'name' => $subject->name,
        'code' => $this->parse_course_code($subject->code, $curriculum->grade->name),
        'student_grade_id' => $curriculum->student_grade_id,
        'schema' =>  config('edu.default.course_schema')
      ];

      $course = Course::firstOrNew(array_only($data,['code','tenant_id']));

      $course->fill($data);

      $course->save();

      \Log::info('Course '.$course->id.' updated');

      return $course;
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  public function create_student($request){
    try{
      $data = array_merge($request->all(), [
        'address' => $request->address,
        'meta' => [
          'student_grade_id' => $request->student_grade_id,
        ],
        'tenant_id' => $this->tenant->id,
      ]);

      unset($data['student_grade_id']);

      $student = Student::create($data);

      if ($request->has('guardian_id')) {
        $guardian_id = $request->guardian_id;
        $guardian = Guardian::find($guardian_id);

        if ($guardian) {
          $guardian->assignWards([$student->id]);
          $student->append('guardian');
        }
      }

      \Log::info('Created student '.$student->ref_id);

      return $student;
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  public function create_instructor($request){
    try{
      $data = array_merge($request->all(), [
        'tenant_id' => $this->tenant->id,
      ]);

      if ($request->has('address')) {
        $data['address'] = $request->address;
      }

      $instructor = Instructor::create($data);

      \Log::info('Created instructor '.$instructor->id);

      return $instructor;
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  public function create_guardian(Request $request){
    try{
      $data = array_merge($request->except('ward_ids'), [
        'address' => $request->address,
        'tenant_id' => $this->tenant->id,
      ]);

      $guardian = Guardian::create($data);
      
      if ($request->has('ward_ids')) {
        $guardian->assignWards($request->ward_ids);
      }

      \Log::info('Created Guardian '.$guardian->id);

      return $guardian;
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }
}
