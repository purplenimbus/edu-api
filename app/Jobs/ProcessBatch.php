<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Notifications\BatchProcessed;
use Illuminate\Support\Facades\Auth;

use App\Tenant as Tenant;
use App\Nimbus\NimbusEdu as NimbusEdu;

class ProcessBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    var $data;
    var $type;
    var $tenant_id;
    var $payload;
    var $NimbusEdu;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenant_id,$data,$type)
    {
        $this->NimbusEdu = new NimbusEdu($tenant_id);
        $this->tenant_id = $tenant_id;
        $this->data = $data;
        $this->type = $type; //TO DO : Validate this in StoreBatch
        $this->payload = [
            'updated' => [],
            'created' => [],
            'skipped' => []
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $self = $this;
        foreach ($this->data as $data){

            // validate user here;
            $data['tenant_id'] = $self->tenant_id; //why?

            //$data['access_level'] = 1; //All imported users will have level 1 access

            //$data['password'] = $this->createDefaultPassword($data['email']);
            switch($this->type){
                case 'user' : $this->payload = $self->NimbusEdu->processUser($data,$this->payload); break;
                case 'subject' : $this->payload = $self->NimbusEdu->processSubject($data,$this->payload); break;
                case 'coursegrade' : $this->payload = $self->NimbusEdu->processCourseGrade($data,$this->payload); break;
                case 'curriculum' : $this->payload = $self->NimbusEdu->processCurriculum($data,$this->payload); break;
                default : break;
            }
        }

        //var_dump($this->payload);

        $tenant = Tenant::find($this->tenant_id);

        $tenant->notify(new BatchProcessed($this->payload));

        \Log::info('ProcessBatch '.ucfirst($this->type).': '.sizeof($this->payload['created']).' Created , '.sizeof($this->payload['updated']).' Updated for tenant_id: '.$this->tenant_id);
        
    }
}
