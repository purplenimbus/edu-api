<?php

namespace App\Providers;

use App\Observers\BankAccountObserver;
use App\BankAccount;
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
