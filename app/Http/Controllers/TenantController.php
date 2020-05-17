<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Activity;
use App\Tenant;
use App\SchoolTerm;
use App\Http\Requests\UpdateTenant;
use App\Http\Requests\GetTenant;
use App\Nimbus\NimbusEdu as NimbusEdu;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedInclude;
use App\Jobs\CompleteTerm;

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

  public function stats(GetTenant $request){
    $tenant = Auth::user()->tenant()->first();

    $terms = QueryBuilder::for(SchoolTerm::class)
      ->allowedFields([
        'registrations'
      ])
      ->allowedIncludes([
        'registrations',
      ]);

    return response()->json($terms, 200);
  }

  public function updateTerm(UpdateTerm $request){
    $tenant = Auth::user()->tenant()->first();

    if ($request->status_id === 2) {
      CompleteTerm::dispatch($tenant);
    }

    return response()->json(['message' => 'your request is being processed'],200);
  }
}
