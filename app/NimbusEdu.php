<?php

namespace App\Nimbus;

use App\Tenant as Tenant;
use App\User as User;
use App\Subject as Subject;
use App\Course as Course;
use App\Curriculum as Curriculum;
use App\CourseGrade as CourseGrade;
use App\Registration as Registration;

class NimbusEdu
{
 	var $tenant_id;

 	public function __construct($tenant_id)
    {
    	$this->tenant_id = $tenant_id;
    }

    public function processUser($data,$payload){
        try{
	        //'\App'::make('App\\'.ucfirst($this->type))

	        $self = $this;
	        $user = User::with('usertype')->firstOrNew(array_only($data, ['firstname','lastname','email','tenant_id']));

	        if($user->id){
	            $payload['updated'][] = $user;
	        }else{
	            $data['access_level'] = 1;

	            $data['password'] = $this->createDefaultPassword($data['email']);

	            $payload['created'][] = $user;
	        }

	        $user->fill($data);

	        $user->save();


	        if($user->has('usertype')){
	            switch($user->usertype->name){
	                case 'student' :    if($user->meta->course_grade_id){ 
	                                        $self->registerStudent($user); 
	                                    } 

	                                    break;

	                case 'teacher' : if(isset($user->meta->course_codes)){ 
	                                    foreach (explode(',',$user->meta->course_codes) as $course_code){

	                                        $course = Course::with(['grade','registrations'])->where('code',$course_code)->first();

	                                        if(isset($course->id)){

	                                            //if(sizeof($course->registrations)){
	                                                $self->assignInstructor($user,$course);
	                                            //}else{
	                                               // \Log::info('Cant assign instructor '.$course_code.' , no students registered ');
	                                            //} 
	                                            
	                                        }else{
	                                            \Log::info('Cant assign instructor, '.$course_code.' not found ');
	                                        }

	                                        unset($user->meta->course_codes);

	                                        $user->save();
	                                        
	                                    }
	                                }

	                                break;
	            }
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

	        $curriculum = Curriculum::firstOrNew(array_only($data, ['course_grade_id']));

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

	        $curriculum->fill($data);

	        $curriculum->save();

	        return $payload;
        }catch(Exception $e){
        	throw new Exception($e->getMessage());
        }
    } 

    public function registerStudent(User $user){

        try{
        	foreach ($this->getCourseLoadIds($user->meta->course_grade_id)['core'] as $course) {
	            $registration = Registration::firstOrNew([
	                'tenant_id' => $this->tenant_id ,
	                'user_id' => $user->id ,
	                'course_id' => $course['id'],
	                //set term id here
	            ]);

	            $registration->save();

	            \Log::info('Student '.$user->id.' Registered in '.$course['code'].' , Registration UUID'.$registration->uuid);
	        }

	        return $registration;
        }catch(Exception $e){
        	throw new Exception($e->getMessage());
        }
    }

    public function assignInstructor(User $user,Course $course){
    	try{
	        $course->instructor_id = $user->id;

	        $course->save();

	        \Log::info('Instructor '.$user->id.' Assigned to '.$course->code);

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
	            'tenant_id' => $this->tenant_id,
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
}