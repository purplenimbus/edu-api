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
use App\Observers\PaymentProfileObserver;
use App\Observers\TenantObserver;
use App\Tenant;
use App\Registration;
use App\Observers\RegistrationObserver;
use App\Observers\SchoolTermObserver;
use App\Observers\StudentObserver;
use App\Observers\UserObserver;
use App\PaymentProfile;
use App\SchoolTerm;
use App\Student;
use App\User;
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

    BankAccount::observe(BankAccountObserver::class);
    Course::observe(CourseObserver::class);
    Guardian::observe(GuardianObserver::class);
    Instructor::observe(InstructorObserver::class);
    PaymentProfile::observe(PaymentProfileObserver::class);
    Registration::observe(RegistrationObserver::class);
    Student::observe(StudentObserver::class);
    Tenant::observe(TenantObserver::class);
    User::observe(UserObserver::class);
    SchoolTerm::observe(SchoolTermObserver::class);
  }
}
