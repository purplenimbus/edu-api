<?php

namespace App\Providers;

use App\BankAccount;
use App\Course;
use App\Observers\BankAccountObserver;
use App\Observers\CourseObserver;
use App\Guardian;
use App\Observers\GuardianObserver;
use App\Instructor;
use App\Observers\InstructorObserver;
use App\Observers\TenantObserver;
use App\Tenant;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [
    'App\Events\Event' => [
      'App\Listeners\EventListener',
    ],
  ];

  /**
   * Register any events for your application.
   *
   * @return void
   */
  public function boot()
  {
    parent::boot();

    Course::observe(CourseObserver::class);
    Guardian::observe(GuardianObserver::class);
    BankAccount::observe(BankAccountObserver::class);
    Instructor::observe(InstructorObserver::class);
    Tenant::observe(TenantObserver::class);
  }
}
