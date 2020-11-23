<?php

namespace App\Providers;

use App\Observers\BankAccountObserver;
use App\BankAccount;
use App\Observers\CourseObserver;
use App\Course;
use App\Observers\GuardianObserver;
use App\Guardian;
use App\Observers\InstructorObserver;
use App\Instructor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		BankAccount::observe(BankAccountObserver::class);
		Course::observe(CourseObserver::class);
		Guardian::observe(GuardianObserver::class);
		Instructor::observe(InstructorObserver::class);
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
			//
	}
}
