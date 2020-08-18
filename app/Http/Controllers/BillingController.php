<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Billing as Billing;

class BillingController extends Controller
{
	public function getBills($tenant_id, Request $request)
	{
		$query = [
			['tenant_id', '=', $tenant_id]
		];

		$relationships = ['registrations', 'status'];

		$bills = $request->has('paginate') ?
			Billing::with($relationships)
			->where($query)
			->paginate($request->paginate)

			: Billing::with($relationships)
			->where($query)
			->get();

		if (sizeof($bills)) {
			return response()->json($bills, 200);
		} else {

			$message = 'no billing found for tenant id : ' . $tenant_id;

			return response()->json(['message' => $message], 204);
		}
	}
}
