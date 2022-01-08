<?php

namespace App\Jobs;

use App\NimbusEdu\Helpers\importsUser;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Auth;

use App\Tenant;
use App\NimbusEdu\NimbusEdu;
use App\NimbusEdu\Syllabus;

class ProcessBatch implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, importsUser;

  var $data;
  var $type;
  var $tenant;
  var $payload;
  var $NimbusEdu;
  var $Syllabus;
  var $author;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Tenant $tenant, array $data, string $type)
  {
    
    $this->tenant = $tenant;
    $this->NimbusEdu = new NimbusEdu($this->tenant);
    $this->Syllabus = new Syllabus($this->tenant);
    $this->data = $data;
    $this->type = $type; //TO DO : Validate this in StoreBatch
    $this->payload = [
      'updated' => [],
      'created' => [],
      'skipped' => []
    ];

    $this->author = Auth::user();
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    foreach ($this->data as $data){
      $data['tenant_id'] = $this->tenant->id;

      switch($this->type){
        case 'student' : $this->importStudent($data, $this->tenant); break;
        case 'instructor' : $this->importInstructor($data, $this->tenant); break;
        case 'guardian' : $this->importGuardian($data, $this->tenant); break;
        //need to deprecate these cases below as they are on longer being used 
        case 'results' : $this->payload = $this->NimbusEdu->processResults($data, $this->payload); break;
        case 'course' : $this->payload = $this->Syllabus->processCourses($data); break;
      }
    }
  }
}
