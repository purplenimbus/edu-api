<?php

namespace App\Nimbus;

use App\Tenant;
use App\User;
use App\Student;
use App\Instructor;
use App\Subject;
use App\Course;
use App\Curriculum;
use App\CourseGrade;
use App\Registration;
use App\SchoolTerm;
use App\CurriculumType;
use App\UserType;
use App\StatusType;
use App\Billing;
use App\Guardian;
use App\Notifications\ActivateUser;
use App\Notifications\BatchProcessed;
use Exception;

class NimbusEdu
{
  var $tenant;

  public function __construct(Tenant $tenant)
  {
    $this->tenant = $tenant;
  }

  public function processUser($data, $payload){
    try{
      $self = $this;
      $user = User::with(['status_type'])
        ->firstOrNew(array_only($data, ['firstname','lastname','email','tenant_id']));

      $created = isset($user->id) ? false : true;

      $user_type = $this->getUserType($data['meta']['user_type']);

      if(!isset($user->id)){
        $data['password'] = $user->createDefaultPassword();
      }

      $data['user_type_id'] = $user_type->id;

      $user_type = $data['meta']['user_type'];

      if(isset($data['meta']['course_codes'])){
        $course_codes =  $data['meta']['course_codes'];
        unset($data['meta']['course_codes']);
      }else{
        $course_codes = false;
      }

      unset($data['meta']['user_type']);

      $user->fill($data);

      $user->save();

      switch($user_type){
        case 'student' : $self->enrollCoreCourses($user, $user->meta->course_grade_id); 

        break;

        case 'teacher' : if($course_codes){ 
          foreach (explode(',',$course_codes) as $course_code){
            $course = Course::with(['grade','registrations'])->where('code',$course_code)->first();

            if(isset($course->id)){
              $self->assignInstructor($user, $course);
            }else{
              \Log::info('Cant assign instructor, '.$course_code.' not found ');
            }
          }
        }

        break;

        case 'admin' : $user->assignRole('admin'); break;
        case 'parent' : $user->assignRole('parent'); break;
        default : $user->assignRole('other'); break;
      }

      if($created){
        $payload['created'][] = $user;
      }else{
        $payload['updated'][] = $user;
      }

      return $payload;

    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
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

  public function processCurriculum($data, $payload){
    try{
      $course_load = [
        'core' => [],
        'optional' => [],
        'elective' => [],
      ];

      $query = array_only($data, ['course_grade_id']);

      $curriculum_type = $this->getCurriculumType();

      $query['type_id'] = $curriculum_type->id;

      $curriculum = Curriculum::firstOrNew($query);

      $new = isset($curriculum->id) ? $curriculum->id : false;

      if(isset($data['core_subjects_code'])){
        $course_load['core'] = $this->parseSubjects($data['core_subjects_code'], $curriculum, true, $payload);
      }

      if(isset($data['elective_subjects_code'])){
        $course_load['elective'] = $this->parseSubjects($data['elective_subjects_code'], $curriculum, true, $payload);
      }

      if(isset($data['optional_subjects_code'])){
        $course_load['optional'] = $this->parseSubjects($data['optional_subjects_code'], $curriculum, true, $payload);
      }

      $curriculum->course_load = $course_load;

      $curriculum->save();

      if($new){
        $payload['updated'][] = $curriculum;
      }else{
        $payload['created'][] = $curriculum;
      }

      return $payload;

    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  public function processCourseGrade($data, $payload){
    try{
      $curriculum = CourseGrade::firstOrNew(array_only($data, ['name']));

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

  public function getUserType($name, $new = false){
    return $new ? 
    UserType::firstOrCreate(['name' => strtolower($name)]) : 
    UserType::where(['name' => strtolower($name)])->first();
  }

  public function getCurriculumType($new = false){
    return $new ? 
    CurriculumType::firstOrCreate(['country' => $this->tenant->country]) : 
    CurriculumType::where(['country' => $this->tenant->country])->first();
  }

  public function getStatusID($name,$new = false){
    return  $new ? 
    StatusType::firstOrCreate(['name' => $name]) : 
    StatusType::where(['name' => $name])->first();
  }

  public function enrollCoreCourses(Student $student, $course_grade_id){
    try{
      var_dump('Attempting to enroll student: '.$student->id);

      $school_term = $this->tenant->current_term;

      $billing = Billing::firstOrCreate([
        'tenant_id' => $this->tenant->id,
        'term_id' => $school_term->id
      ]);

      foreach ($this->getCourseLoadIds($course_grade_id)['core'] as $course) {

        var_dump('Enrolling '.$student->ref_id.' in '.$course['code']);

        $registration = Registration::firstOrCreate([
          'tenant_id' => $this->tenant->id ,
          'user_id' => $student->id ,
          'course_id' => $course['id'],
          'term_id' => $school_term->id,
          'billing_id' => $billing->id
        ]);

        $student->account_status_id = $this->getStatusID('registered')->id;

        $student->save();

        \Log::info('Student '.$student->id.' Registered in '.$course['code'].' , Registration ID '.$registration->id);

        var_dump('Student '.$student->id.' Registered in '.$course['code'].' , Registration ID '.$registration->id);
      }
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  public function assignInstructor(User $instructor, Course $course){
    try{
      $course->instructor_id = $instructor->id;

      $course->save();

      \Log::info('Instructor '.$instructor->id.' Assigned to '.$course->code);

      $instructor->account_status_id = $this->getStatusID('assigned')->id;

      $instructor->save();

      return $course;
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  private function getCourseLoadIds($course_grade_id){
    try{
      $curriculum = Curriculum::with('grade')->where('course_grade_id',$course_grade_id)->first();

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
     throw new Exception('No Curriculum found with grade id '+$course_grade_id);
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
        'course_grade_id' => $curriculum->course_grade_id,
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
          'course_grade_id' => $request->course_grade_id,
        ],
        'tenant_id' => $this->tenant->id,
      ]);

      unset($data['course_grade_id']);

      $student = Student::create($data);

      if ($request->has('guardian_id')) {
        $guardian_id = $request->guardian_id;
        $guardian = Guardian::find($guardian_id);

        if ($guardian) {
          $guardian->assignWards([$student->id]);
          $student->append('guardian');
        }
      }

      $student->notify(new ActivateUser);

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

      $instructor->notify(new ActivateUser);

      \Log::info('Created instructor '.$instructor->id);

      return $instructor;
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  public function create_guardian($request){
    try{
      $data = array_merge($request->except('ward_ids'), [
        'address' => $request->address,
        'tenant_id' => $this->tenant->id,
      ]);

      $guardian = Guardian::create($data);

      $guardian->notify(new ActivateUser);
      
      if ($request->has('ward_ids')) {
        $guardian->assignWards($request->ward_ids);
      }

      $guardian->load('wards.members.user');

      \Log::info('Created Guardian '.$guardian->id);

      return $guardian;
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }
}
