<?php

namespace App\Nimbus;

use App\Tenant as Tenant;
use App\SchoolTerm as SchoolTerm;

use App\Jobs\ProcessBatch;

class Institution extends NimbusEdu
{
  var $tenant;
  var $country_name;

  public function __construct(Tenant $tenant,$country_name)
  {
    $this->tenant = $tenant;

    $this->country_name = $country_name;

    switch($this->country_name){
      default :   $subjects = $this->generate('subjects.json','subject');
      $course_grades = $this->generate('course_grades.json','coursegrade');
      $curricula = $this->generate('curricula.json','curriculum');

      break;
    }

    $school_term =  SchoolTerm::create(['tenant_id' => $this->tenant->id,'name' => 'first','year' => 2018]);
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
      echo 'Generating new '.$type.' for tenant : '.$this->tenant->name."\r\n";

      ProcessBatch::dispatch($this->tenant, $this->readJson($path),$type);
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }

  }
}
