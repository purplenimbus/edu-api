<?php

namespace App\Observers;

use App\Tenant;
use App\Nimbus\Institution;

class TenantObserver
{
	/**
	 * Handle the tenant "created" event.
	 *
	 * @param  \App\Tenant  $tenant
	 * @return void
	 */
	public function created(Tenant $tenant)
	{
		$institution = new Institution($tenant);
	}

	/**
	 * Handle the tenant "updated" event.
	 *
	 * @param  \App\Tenant  $tenant
	 * @return void
	 */
	public function updated(Tenant $tenant)
	{
			//
	}

	/**
	 * Handle the tenant "deleted" event.
	 *
	 * @param  \App\Tenant  $tenant
	 * @return void
	 */
	public function deleted(Tenant $tenant)
	{
			//
	}

	/**
	 * Handle the tenant "restored" event.
	 *
	 * @param  \App\Tenant  $tenant
	 * @return void
	 */
	public function restored(Tenant $tenant)
	{
		//
	}

	/**
	 * Handle the tenant "force deleted" event.
	 *
	 * @param  \App\Tenant  $tenant
	 * @return void
	 */
	public function forceDeleted(Tenant $tenant)
	{
		//
	}
}
