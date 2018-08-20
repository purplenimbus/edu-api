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

class ProcessBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    var $data;
    var $type;
    var $tenant_id;
    var $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenant_id,$data,$type)
    {
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

        foreach ($this->data as $user){

            // validate user here;
            $user['tenant_id'] = $self->tenant_id;

            //$user['access_level'] = 1; //All imported users will have level 1 access

            //$user['password'] = $this->createDefaultPassword($user['email']);

            $resource = '\App'::make('App\\'.ucfirst($this->type))
                            ->firstOrNew(array_only($user, ['firstname','lastname','email','tenant_id']));

            if($resource->id){
                $self->payload['updated'][] = $resource;
            }else{
                $user['access_level'] = 1;

                $user['password'] = $this->createDefaultPassword($user['email']);

                $self->payload['created'][] = $resource;
            }

            $resource->fill($user);

            $resource->save();

            \Log::info('ProcessBatch Resource Created , id : {$resource->id} , tenant_id: {$this->tenant_id} type:{$this->type}');
        }

        $tenant = Tenant::find($this->tenant_id);

        $tenant->notify(new BatchProcessed($this->payload));
        
    }

    private function createDefaultPassword($str = false){
        return app('hash')->make($str);
    }
}
