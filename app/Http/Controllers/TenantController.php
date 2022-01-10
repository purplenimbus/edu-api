<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateTenant;
use App\Http\Requests\GetTenant;
use App\Http\Requests\GetTenantSettings;
use App\Http\Requests\UpdateTenantSetting;
use Spatie\QueryBuilder\QueryBuilder;
use App\Tenant;
use Storage;

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
        'payment_details',
      ])
      ->allowedIncludes([
        'school_term_types',
        'payment_profile_item_types'
      ])
      ->where([
        ['id', '=', $request->id]
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
    $disk = Storage::disk('s3');

    $tenant->fill($request->except('logo'));

    if ($request->has('logo') && !is_null($request->logo)) {
      $extension = $request->logo->extension();
      $file_name = $tenant->id.".{$extension}";

      $path = $request->logo->storeAs('tenant_avatars', $file_name, 's3');

      $tenant->logo = $disk->url($path);
    }

    if ($request->has('logo') && is_null($request->logo)) {
      $tenant->logo = null;
      $file_name_png = "tenant_avatars\\{$tenant->id}.png";
      $file_name_jpg = "tenant_avatars\\{$tenant->id}.jpg";

      if ($disk->has($file_name_png)) {
        $disk->delete($file_name_png);
      }

      if ($disk->has($file_name_jpg)) {
        $disk->delete($file_name_jpg);
      }
    }

    $tenant->save();

    return response()->json($tenant, 200);
  }
  
  public function getSettings(GetTenantSettings $request){
    $tenant = Auth::user()->tenant()->first();

    return response()->json($tenant->settings()->get($request->setting_name), 200);
  }

  public function updateSetting(UpdateTenantSetting $request){
    $tenant = Auth::user()->tenant()->first();

    $tenant->settings()->update($request->name, $request->value);

    return response()->json($tenant->settings()->get($request->setting_name), 200);
  }
}
