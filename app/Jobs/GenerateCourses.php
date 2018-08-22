<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Course as Course;
use App\Tenant as Tenant;

use App\Notifications\CoursesGenerated;
use Illuminate\Support\Facades\Auth;

class GenerateCourses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    var $subjects;
    var $tenant_id;
    var $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenant_id,$subjects)
    {
        $this->subjects = $subjects;
        $this->tenant_id = $tenant_id;
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

        foreach ($this->subjects as $subject) {

            $data = [
                'subject_id' => $subject->id,
                'tenant_id' => $self->tenant_id,
                'name' => $subject->name,
                'code' => $subject->code
            ];

            $course = Course::firstOrNew(array_only($data,['name']));

            if($course->id){
                $self->payload['updated'][] = $course;
            }else{
                $self->payload['created'][] = $course;
            }

            $course->fill($data);

            $course->save();

        }

        $tenant = Tenant::find($self->tenant_id);

        $tenant->notify(new CoursesGenerated($this->payload));

        \Log::info('GenerateCourses Created');
    }
}
