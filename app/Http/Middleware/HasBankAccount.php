<?php

namespace App\Http\Middleware;

use Closure;


use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class HasBankAccount
{
		/**
		 * Handle an incoming request.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 * @param  \Closure  $next
		 * @return mixe
		 */
		public function handle($request, Closure $next)
		{
			$user = Auth::user();
			$tenant = $user->tenant()->first();
			if (!$tenant->has_bank_account){
				throw new AuthorizationException(__('validation.custom.bank_account.not_set'));
			}
			return $next($request);
		}
}
