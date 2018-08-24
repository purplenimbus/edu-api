<?php

namespace App\Nimbus;

use App\Tenant as Tenant;
use App\User as User;
use App\Subject as Subject;
use App\Course as Course;
use App\Curriculum as Curriculum;
use App\CourseGrade as CourseGrade;
use App\Registration as Registration;

class Institution extends NimusEdu
{
 	$this->tenant_id = $tenant_id;

 	public function __construct($tenant_id,$country)
    {
    	$this->tenant_id = $tenant_id;

    	//add classes

    	//add subjects

    	//add curriculum
    }
}