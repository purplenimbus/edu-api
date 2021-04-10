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

    if ($tenant->schoolTermTypes->count() > 0 && $request->flat_fee == true) {
      $this->createPaymentProfilesForAllTerms($request);

      $paymentProfile = $tenant->payment_profiles->last()->load('items');
    } else {
      $paymentProfile = new PaymentProfile();

      $request->merge([
        'tenant_id' => $tenant->id,
      ]);

      $paymentProfile->fill($request->except('items'));
      
      $paymentProfile->save();

      if ($request->has('items')) {
        foreach ($request->items as $item) {
          $item['tenant_id'] = $tenant->id;

          $paymentProfile->items()->create($item);
        }

        $paymentProfile->load('items');
      }
    }
    

    return response()->json($paymentProfile, 200);
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

    $payment_profile->fill($request->except('items'));

    $payment_profile->save();

    if ($request->has('items')) {
      $payment_profile->items()->delete();

      foreach ($request->items as $item) {
        $item['tenant_id'] = $tenant->id;

        $payment_profile->items()->create($item);
      }
    }

    return response()->json($payment_profile->load('items'), 200);
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

  private function createPaymentProfilesForAllTerms($request) {
    $tenant = Auth::user()->tenant()->first();

    foreach ($tenant->schoolTermTypes as $schoolTermType) {
      $payment_profile = new PaymentProfile();

      $request->merge([
        'tenant_id' => $tenant->id,
        'school_term_type_id' => $schoolTermType->id,
      ]);

      $payment_profile->fill($request->except('items'));
      
      $payment_profile->save();

      if ($request->has('items')) {
        foreach ($request->items as $item) {
          $item['tenant_id'] = $tenant->id;

          $payment_profile->items()->updateOrCreate($item);
        }
      }
    }
  }
}
