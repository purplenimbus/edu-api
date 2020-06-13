<?php

namespace App\Nimbus;

use App\Tenant as Tenant;
use App\SchoolTerm as SchoolTerm;
use Carbon\Carbon;
use App\Jobs\ProcessBatch;

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

  public function generateSubjects() {
    $this->generate('subjects.json', 'subject');
  }

  public function generateClasses() {
    $this->generate('course_grades.json', 'coursegrade');
  }

  public function generateCurriculum() {
    $this->generate('curricula.json', 'curriculum');;
  }

  private function readJson($path){
    try{
      return json_decode(file_get_contents($path),true);
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }
  }

  private function generate($path, $type){
    try{
      echo 'Generating new '.$type.' for tenant : '.$this->tenant->name."\r\n";

      ProcessBatch::dispatch($this->tenant, $this->readJson($path),$type);
    }catch(Exception $e){
      throw new Exception($e->getMessage());
    }

  }
}
