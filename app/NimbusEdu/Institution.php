<?php

namespace App\Nimbus;

use App\Tenant as Tenant;
use App\User as User;
use App\Subject as Subject;
use App\Course as Course;
use App\Curriculum as Curriculum;
use App\CourseGrade as CourseGrade;
use App\Registration as Registration;

use App\Jobs\ProcessBatch;

class Institution extends NimbusEdu
{
 	var $tenant_id;

 	var $country_name;

 	public function __construct($tenant_id,$country_name)
    {
    	$this->tenant_id = $tenant_id;

    	$this->country_name = $country_name;

    	switch($this->country_name){
    		default : 	$subjects = $this->generate('subjects.json','subject');
    					$course_grades = $this->generate('course_grades.json','coursegrade');

    					break;
    	}
    }

    private function readJson($path){
    	try{

    		return json_decode(file_get_contents($path),true);

    	}catch(Exception $e){
    		throw new Exception($e->getMessage());
    	}
    }

    public function generate($path,$type){

    	try{

    		ProcessBatch::dispatch($this->tenant_id,$this->readJson($path),$type);


    	}catch(Exception $e){
    		throw new Exception($e->getMessage());
    	}

    }
}