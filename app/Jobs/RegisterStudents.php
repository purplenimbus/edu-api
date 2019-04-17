<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Notifications\StudentsRegistered;

use App\Tenant as Tenant;
use App\User as User;

class RegisterStudents implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  var $data;
  var $tenant;
  var $payload;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Tenant $tenant, $data)
  {
    $this->tenant = $tenant;
    $this->data = $data;
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
    $this->tenant->notify(new StudentsRegistered($this->payload));
  }
}
