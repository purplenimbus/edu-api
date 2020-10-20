<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateTenant;
use App\Http\Requests\GetTenant;
use Spatie\QueryBuilder\QueryBuilder;
use App\Tenant;

class TenantController extends BaseController
{
  /**
   * Show a tenant
   *
   * @return void
   */
  public function show(GetTenant $request){    
    $tenant = QueryBuilder::for(Tenant::class)
      ->allowedAppends([
        'current_term',
        'owner',
        'payment_details'
      ])
      ->where([
        ['id', '=', $request->tenant_id]
      ])
      ->first();

    return response()->json($tenant, 200);
  }

  /**
   * Edit a new tenant
   *
   * @return void
   */
  public function update(UpdateTenant $request){
    $tenant = Auth::user()->tenant()->first();
    
    $tenant->fill($request->all());

    $tenant->save();

    return response()->json($tenant, 200);
  }
  
  public function settings(GetTenant $request){
    $tenant = Auth::user()->tenant()->first();

    return response()->json($tenant->meta->settings, 200);
  }
}
