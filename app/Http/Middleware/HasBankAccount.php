<?php

namespace App\Http\Middleware;

use Closure;

use \App\Tenant as Tenant;

class HasBankAccount
{
		/**
		 * Handle an incoming request.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 * @param  \Closure  $next
		 * @return mixed
		 */
		public function handle($request, Closure $next)
		{
			$tenant = new Tenant();
			$hasbankaccount = $tenant->has_bank_account();      
			if ( $hasbankaccount == FALSE){
				return response()->json($hasbankaccount, 403);
			}
			return $next($request);
		}
}
