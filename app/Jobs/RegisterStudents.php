<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Tenant as Tenant;
use App\User as User;

class RegisterStudents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    var $data;
    var $tenant_id;
    var $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        $tenant = Tenant::find($this->tenant_id);

        $tenant->notify(new BatchProcessed($this->payload));

        var_dump($tenant);
    }
}
