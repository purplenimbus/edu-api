<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User as User;
use App\Activity as Activity;
use App\Tenant as Tenant;
use App\Transaction as Transaction;
use App\Service as Service;
use App\Http\Requests\UpdateTenant;
use App\Http\Requests\GetTenant;
use App\Nimbus\NimbusEdu as NimbusEdu;

class TenantController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
