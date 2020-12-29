<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Notifications\BatchProcessed;

use Illuminate\Support\Facades\Auth;

use App\Tenant;
use App\Nimbus\NimbusEdu;
use App\Nimbus\Syllabus;

class ProcessBatch implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
  public function __construct(Tenant $tenant, $data, $type)
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
    $self = $this;
    $payload = [];
    $resource = [];

    foreach ($this->data as $data){
      $data['tenant_id'] = $self->tenant->id;

      switch($this->type){
        case 'user' : $this->payload = $self->NimbusEdu->processUser($data, $this->payload); break;
        case 'results' : $this->payload = $self->NimbusEdu->processResults($data, $this->payload); break;
        case 'course' : $this->payload = $self->Syllabus->processCourses($data); break;

        $resource =  isset($this->payload['resource']) ? 
        $this->payload['resource'] 
        : [];

        break;
        default : break;
      }
    }

    foreach ($this->payload as $key => $value) {
      $payload[$key] = sizeof($value);
    }

    $payload['batch_type'] = $this->type;

    if ($this->author) {
      $payload['author'] = $this->author->only(['id','firstname','lastname']);
    }

    $payload['resource'] = $resource;

    $this->tenant->notify(new BatchProcessed($payload));

    \Log::info('ProcessBatch '.ucfirst($this->type).': '.sizeof($this->payload['created']).' Created , '.sizeof($this->payload['updated']).' Updated for tenant_id: '.$this->tenant->id);

  }
}
