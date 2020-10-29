<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyTransaction;
use App\Invoice;
use App\InvoiceStatus;
use App\Transaction;
use App\TransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
	public function verify(VerifyTransaction $request) {
		$tenant = Auth::user()->tenant()->first();

		$data = paystack()->getPaymentData();

		if ($data) {
			$status = Arr::get($data, 'data.status', null);

			$transaction_status = TransactionStatus::where('name', $status)->first();

			$payload = [
				'amount' => Arr::get($data, 'data.amount', null),
				'authorization_code' => Arr::get($data, 'data.authorization.authorization_code', null),
				'invoice_id' => $request->invoice_id,
				'paid_at' => Arr::get($data, 'data.paid_at', null),
				'ref_id' => Arr::get($data, 'data.id', null),
				'status_id' => $transaction_status->id,
				'tenant_id' => $tenant->id,
			];

			$transaction = Transaction::updateOrCreate(Arr::only($payload, ['ref_id', 'invoice_id']), Arr::except($payload, ['ref_id', 'invoice_id']));

			//TO DO will need to run calculations against the invoice in a more dynamic way to determine if the invoice is paid off or not
			$invoice_status = InvoiceStatus::where('name', 'paid')->first();

			$transaction->invoice->status_id = $invoice_status->id;

			$transaction->invoice->save();

			return response()->json($transaction, 200);
		}
	}
}
