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
use App\User as User;
use App\Subject as Subject;
use App\Course as Course;
use App\Curriculum as Curriculum;
use App\CourseGrade as CourseGrade;

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
        foreach ($this->data as $data){

            // validate user here;
            $data['tenant_id'] = $self->tenant_id;

            //$data['access_level'] = 1; //All imported users will have level 1 access

            //$data['password'] = $this->createDefaultPassword($data['email']);
            switch($this->type){
                case 'user' : $this->payload = $self->processUser($data,$this->payload); break;
                case 'subject' : $this->payload = $self->processSubject($data,$this->payload); break;
                case 'coursegrade' : $this->payload = $self->processCourseGrade($data,$this->payload); break;
                case 'curriculum' : $this->payload = $self->processCurriculum($data,$this->payload); break;
                default : break;
            }
        }

        $tenant = Tenant::find($this->tenant_id);

        $tenant->notify(new BatchProcessed($this->payload));

        \Log::info('ProcessBatch '.ucfirst($this->type).': '.sizeof($this->payload['created']).' Created , '.sizeof($this->payload['updated']).' Updated for tenant_id: '.$this->tenant_id);
        
    }

    private function processUser($data,$payload){
        //'\App'::make('App\\'.ucfirst($this->type))
        $user = User::firstOrNew(array_only($data, ['firstname','lastname','email','tenant_id']));

        if($user->id){
            $payload['updated'][] = $user;
        }else{
            $data['access_level'] = 1;

            $data['password'] = $this->createDefaultPassword($data['email']);

            $payload['created'][] = $user;
        }

        $user->fill($data);

        $user->save();

        return $payload;
    }  

    private function processSubject($data,$payload){
        //'\App'::make('App\\'.ucfirst($this->type))
        $subject = Subject::firstOrNew(array_only($data, ['name']));

        if($subject->id){
            $payload['updated'][] = $subject;
        }else{

            $payload['created'][] = $subject;
        }

        $subject->fill($data);

        $subject->save();

        return $payload;
    }

    private function processCurriculum($data,$payload){
        //'\App'::make('App\\'.ucfirst($this->type))
        $curriculum = Curriculum::firstOrNew(array_only($data, ['name']));

        if($curriculum->id){
            $payload['updated'][] = $curriculum;
        }else{

            $payload['created'][] = $curriculum;
        }

        $curriculum->fill($data);

        $curriculum->save();

        return $payload;
    }

    private function processCourseGrade($data,$payload){
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
    }   

    private function createDefaultPassword($str = false){
        return app('hash')->make($str);
    }
}
