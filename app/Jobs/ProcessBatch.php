<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    var $data;
    var $type;
    var $tenant_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenant_id,$data,$type)
    {
        $this->data = $data;
        $this->type = $type;
        $this->tenant_id = $tenant_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('ProcessBatch '.$this->data.', tenant_id: '+$this->tenant_id.', type:'.$this->type);
    }
}
