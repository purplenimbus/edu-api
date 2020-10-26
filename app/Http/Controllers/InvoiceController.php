<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteInvoice;
use App\Http\Requests\GetInvoices;
use App\Http\Requests\GetInvoice;
use App\Http\Requests\StoreInvoice;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Invoice;
use App\InvoiceStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder as Builder;

class InvoiceController extends Controller
{
	/**
   * List all Invoices
   *
   * @return void
   */
	public function index(GetInvoices $request) {
		$tenant = Auth::user()->tenant()->first();

		$invoices = QueryBuilder::for(Invoice::class)
			->allowedIncludes([
				'line_items',
				'recipient',
				'status',
			])
			->allowedAppends([
				'balance',
				'formatted_balance',
			])
			->allowedFilters([
        'name',
        AllowedFilter::callback('recipient_id', function (Builder $query, $value) {
          return $query->where('recipient_id', '=', (int)$value);
        }),
        AllowedFilter::callback('status', function (Builder $query, $value) {
          $status = InvoiceStatus::where('name', $value)->first();

          return $query->where('status_id', '=', isset($status->id) ? (int)$status->id: false);
        }),
        AllowedFilter::callback('status_id', function (Builder $query, $value) {
          return $query->where('status_id', '=', (int)$value);
				}),
			])
			->where([
				['tenant_id', '=', $tenant->id]
			]);

		$data = isset($request->paginate) ? $invoices->paginate($request->paginate) : $invoices->get();

		return response()->json($data, 200);
	}


	/**
   * Show an Invoice
   *
   * @return void
   */
	public function show(GetInvoice $request) {
		$tenant = Auth::user()->tenant()->first();

		$invoice = QueryBuilder::for(Invoice::class)
			->allowedIncludes([
				'line_items',
				'recipient',
				'status',
			])
			->where([
				['tenant_id', '=', $tenant->id],
				['id', '=', $request->invoice_id],
			])
			->first();

		return response()->json($invoice, 200);
	}

	/**
   * Create an Invoice
   *
   * @return void
   */
	public function create(StoreInvoice $request) {
		$tenant = Auth::user()->tenant()->first();

		$request->merge([
			'tenant_id' => $tenant->id,
		]);

		$invoice = Invoice::create($request->only(['recipient_id', 'tenant_id']));

		$line_items = $request->line_items;

		$line_items = data_fill($line_items, '*.tenant_id', $tenant->id);

		$invoice->line_items()->createMany($line_items);
			
		return response()->json($invoice, 200);
	}

	/**
   * Update an Invoices
   *
   * @return void
   */
	public function update(GetInvoice $request) {

	}

	/**
   * delete an Invoice
   *
   * @return void
   */
	public function delete(DeleteInvoice $request) {
		Invoice::destroy($request->invoice_ids);

		return response()->json(true, 200);
	}
}
