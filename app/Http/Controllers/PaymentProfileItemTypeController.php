<?php

namespace App\Http\Controllers;

use App\PaymentProfileItemType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentProfileItemTypeController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $tenant = Auth::user()->tenant()->first();

    $items = QueryBuilder::for(PaymentProfileItemType::class)
      ->defaultSort('name')
      ->ofTenant($tenant->id);

    $data = isset($request->paginate) ? $items->paginate($request->paginate) : $items->get();

    return response()->json($data, 200);
  }
}
