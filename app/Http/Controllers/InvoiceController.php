<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Invoice;

class InvoiceController extends Controller
{
	public function getBills($tenant_id, Request $request)
	{
		$query = [
			['tenant_id', '=', $tenant_id]
		];

		$relationships = ['registrations', 'status'];

		$bills = $request->has('paginate') ?
			Invoice::with($relationships)
			->where($query)
			->paginate($request->paginate)

			: Invoice::with($relationships)
			->where($query)
			->get();

		if (sizeof($bills)) {
			return response()->json($bills, 200);
		} else {

			$message = 'no invoice found for tenant id : ' . $tenant_id;

			return response()->json(['message' => $message], 204);
		}
	}
}
