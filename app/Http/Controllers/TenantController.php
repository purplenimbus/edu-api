<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateTenant;
use App\Http\Requests\GetTenant;

class TenantController extends BaseController
{
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
