<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeletePaymentProfile;
use App\Http\Requests\StorePaymentProfile;
use App\Http\Requests\UpdatePaymentProfile;
use App\PaymentProfile;
use Illuminate\Database\Eloquent\Builder as Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentProfilesController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $tenant = Auth::user()->tenant()->first();

    $profiles = QueryBuilder::for(PaymentProfile::class)
      ->defaultSort('name')
      ->allowedFilters([
        AllowedFilter::exact('student_grade_id'),
        AllowedFilter::exact('school_term_type_id'),
      ])
      ->allowedFields([
        'student_grade'
      ])
      ->allowedIncludes([
        'term_type',
        'items',
        'items.type',
      ])
      ->ofTenant($tenant->id)
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($profiles, 200);
  }

  /**
   * Create the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\PaymentProfile  $payment_profile
   * @return \Illuminate\Http\Response
   */
  public function create(StorePaymentProfile $request)
  {
    $tenant = Auth::user()->tenant()->first();

    $payment_profile = new PaymentProfile();

    $request->merge([
      'tenant_id' => $tenant->id,
    ]);

    $payment_profile->fill($request->all());
    
    $payment_profile->save();

    if ($request->has('items')) {
      foreach ($request->items as $item) {
        $item['tenant_id'] = $tenant->id;

        $payment_profile->items()->updateOrCreate($item);
      }
    }

    return response()->json($payment_profile->load('items'), 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\PaymentProfile  $payment_profile
   * @return \Illuminate\Http\Response
   */
  public function update(UpdatePaymentProfile $request)
  {
    $tenant = Auth::user()->tenant()->first();

    $payment_profile = PaymentProfile::find($request->id);

    $request->merge([
      'tenant_id' => $tenant->id,
    ]);

    $payment_profile->fill($request->all());

    $payment_profile->save();

    if ($request->has('items')) {
      foreach ($request->items as $item) {
        $item['tenant_id'] = $tenant->id;

        $payment_profile->items()->updateOrCreate($item);
      }
    }

    return response()->json($payment_profile, 200);
  }

  /**
   * Delete payment profile
   *
   * @return void
   */
  public function delete(DeletePaymentProfile $request)
  {
    PaymentProfile::destroy($request->id);

    return response()->json(true, 200);
  }
}
