<?php

namespace App\Nimbus;

use App\Tenant as Tenant;
use App\User as User;
use App\Subject as Subject;
use App\Course as Course;
use App\Curriculum as Curriculum;
use App\CourseGrade as CourseGrade;
use App\Registration as Registration;
use App\SchoolTerm as SchoolTerm;
use App\CurriculumType as CurriculumType;
use App\UserType as UserType;
use App\StatusType as StatusType;
use App\Billing as Billing;

class NimbusEdu
{
 	var $tenant;

 	public function __construct($tenant_id = false)
    {
    	if(!$tenant_id){
    		throw new Exception("Tenant id required", 1);	
    	}

    	$this->tenant = Tenant::findOrFail($tenant_id);
    }

    public function processUser($data,$payload){
        try{
	        //'\App'::make('App\\'.ucfirst($this->type))

	        $self = $this;
	        $user = User::with(['user_type','account_status'])->firstOrNew(array_only($data, ['firstname','lastname','email','tenant_id']));
	        $created = isset($user->id) ? false : true;
	        $user_type = $this->getUserType($data['meta']['user_type']);

            //dd($user);

	        if(!isset($user->id)){
	            //$data['access_level'] = 2; //all imported users are granted level 2 access

	            $data['password'] = $this->createDefaultPassword($data['email']);
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

	        $user->access_level_id = 2;

	        $user->save();

	        //var_dump($user->account_status->name != 'registered');

            switch($user_type){
                case 'student' :    /*if(
                						$user->meta->course_grade_id 
                						&& isset($user->account_status->name) 
                						&& $user->account_status->name != 'registered'
                					){ */
                                        $self->enrollCoreCourses($user,$user->meta->course_grade_id); 
                                    //} 

                                    break;

                case 'teacher' : if($course_codes){ 
                                    foreach (explode(',',$course_codes) as $course_code){

                                        $course = Course::with(['grade','registrations'])->where('code',$course_code)->first();

                                        if(isset($course->id)){

                                            //if(sizeof($course->registrations)){
                                                $self->assignInstructor($user,$course);
                                            //}else{
                                               //\Log::info('Cant assign instructor '.$course_code.' , no students registered ');

                                            	//\Log::info('Cant assign instructor '.$course_code.' , instructor already assigned ');
                                            //} 
                                            
                                        }else{
                                            \Log::info('Cant assign instructor, '.$course_code.' not found ');
                                        }
                                        
                                    }
                                }

                                break;
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

    public function processSubject($data,$payload){
    	try{
	        //'\App'::make('App\\'.ucfirst($this->type))
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

    public function processCurriculum($data,$payload){

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
	            $course_load['core'] = $this->parseSubjects($data['core_subjects_code'],$curriculum,true,$payload);
	        }

	        if(isset($data['elective_subjects_code'])){
	            $course_load['elective'] = $this->parseSubjects($data['elective_subjects_code'],$curriculum,true,$payload);
	        }

	        if(isset($data['optional_subjects_code'])){
	            $course_load['optional'] = $this->parseSubjects($data['optional_subjects_code'],$curriculum,true,$payload);
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

    public function processCourseGrade($data,$payload){
        try{
            //'\App'::make('App\\'.ucfirst($this->type))
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

    public function processResults($data,$payload){
        try{
            
            $registration = Registration::findOrFail($data['id']);

            $registration->fill($data);

            $registration->save();

            $payload['updated'][] = $registration;

            return $payload;

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function getUserType($name,$new = false){
    	return $new ? 
                UserType::firstOrCreate(['name' => strtolower($name)]) : 
                UserType::where(['name' => strtolower($name)])->first();
    }

    public function getCurriculumType($new = false){
    	return $new ? 
                CurriculumType::firstOrCreate(['country' => $this->tenant->meta->country]) : 
                CurriculumType::where(['country' => $this->tenant->meta->country])->first();
    }

    public function getStatusID($name,$new = false){
        return  $new ? 
                StatusType::firstOrCreate(['name' => $name]) : 
                StatusType::where(['name' => $name])->first();
    }

    public function getCurrentTerm(){
    	return  SchoolTerm::where(['tenant_id' => $this->tenant->id, 'name' => $this->tenant->meta->current_term ])->first();
    }

    public function enrollCoreCourses(User $user,$course_grade_id){

        try{
        	
        	$school_term = $this->getCurrentTerm();
        	
            $billing = Billing::firstOrCreate([
                'tenant_id' => $this->tenant->id,
                'student_id' => $user->id,
                'term_id' => $school_term->id
            ]);

        	foreach ($this->getCourseLoadIds($course_grade_id)['core'] as $course) {

	            $registration = Registration::firstOrCreate([
	                'tenant_id' => $this->tenant->id ,
	                'user_id' => $user->id ,
	                'course_id' => $course['id'],
	                'term_id' => $school_term->id,
                    'billing_id' => $billing->id
	            ]);

	           	$user->account_status_id = $this->getStatusID('registered')->id;

                if(!$user->ref_id || $user->ref_id == null){
                    $user->ref_id = $this->generateStudentId($user->id);
                }
                
                //dd($user);

	        	$user->save();

	            \Log::info('Student '.$user->id.' Registered in '.$course['code'].' , Registration UUID'.$registration->uuid);
	        }

	        //return $registration;
        }catch(Exception $e){
        	throw new Exception($e->getMessage());
        }
    }

    public function assignInstructor(User $user,Course $course){
    	try{
	        $course->instructor_id = $user->id;

	        $course->save();

	        \Log::info('Instructor '.$user->id.' Assigned to '.$course->code);

	        $user->account_status_id = $this->getStatusID('assigned')->id;

	        $user->save();

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

                                $course = Course::where('code',$this->parseCourseCode($subject->code,$curriculum->grade->name))->first();

                                $course_load[$key][] = $course->only(['id','code']);
                            }
                        }
                    }
                }

                //var_dump($course_load);
                return $course_load;
            }
        }catch(ModelNotFoundException $ex){      
           throw new Exception('No Curriculum found with grade id '+$course_grade_id);
        }
    }

    private function parseCourseCode($subject_code,$grade_name){
        return strtoupper($subject_code.'-'.str_replace(' ','-',$grade_name));
    }

    private function parseSubjects($data,Curriculum $curriculum,$create_course = false){
        try{
	        $parsed = [];

	        $core_subjects_codes = explode(',',$data);

	        foreach ($core_subjects_codes as $core_subject_code) {

	            $core_subject = Subject::where('code',$core_subject_code)->first();

	            //$parsed[] = isset($core_subject->id) ? $core_subject->id : $core_subject_code;

	            if($create_course && isset($core_subject->id) && is_int($core_subject->id)){ 

	                $parsed[] = $core_subject->id;

	                $course = $this->createCourse($core_subject,$curriculum); 

	            }
	            
	        }

	        unset($data);

	        return $parsed;
        }catch(Exception $e){
        	throw new Exception($e->getMessage());
        }
    }

    private function createCourse(Subject $subject,Curriculum $curriculum){

        try{
            $data = [
	            'subject_id' => $subject->id,
	            'tenant_id' => $this->tenant->id,
	            'name' => $subject->name,
	            'code' => $this->parseCourseCode($subject->code,$curriculum->grade->name),
	            'course_grade_id' => $curriculum->course_grade_id,
	            'meta' => [
	                'course_schema' =>  [
	                    'quiz' =>  15,
	                    'midterm' => 30,
	                    'assignment' => 15,
	                    'lab' => 5,
	                    'exam' => 35
	                ]
	            ]
	        ];

	        $course = Course::firstOrNew(array_only($data,['code','tenant_id']));

	        /*if($course->id){
	            $this->payload['updated'][] = $course;
	        }else{
	            $this->payload['created'][] = $course;
	        }*/

	        $course->fill($data);

	        $course->save();

	        \Log::info('Course '.$course->id.' updated');

	        return $course;
        }catch(Exception $e){
        	throw new Exception($e->getMessage());
        }
    }  

    private function createDefaultPassword($str = false){
        return app('hash')->make($str);
    }

    private function generateStudentId($id){
        $user_id = sprintf("%04d", $id);
        return date("Y").$user_id;
    }  
}