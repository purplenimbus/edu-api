<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteLineItem;
use App\Http\Requests\GetLineItem;
use App\LineItem;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Auth;

class LineItemsController extends Controller
{
		/**
   * List all Invoices
   *
   * @return void
   */
	public function index(GetLineItem $request) {
		$tenant = Auth::user()->tenant()->first();

		$line_items = QueryBuilder::for(LineItem::class)
			->where([
				['invoice_id', '=', $request->invoice_id],
				['tenant_id', '=', $tenant->id],
			]);

		$data = isset($request->paginate) ? $line_items->paginate($request->paginate) : $line_items->get();

		return response()->json($data, 200);
	}

	/**
   * delete a Line Item
   *
   * @return void
   */
	public function delete(DeleteLineItem $request) {
		LineItem::destroy($request->line_item_ids);

		return response()->json(true, 200);
	}
}
