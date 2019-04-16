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
use App\Http\Requests\StoreTenant as StoreTenant;
use App\Nimbus\NimbusEdu as NimbusEdu;

class TenantController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function tenants(Request $request){
		
		$tenants = 	$request->has('paginate') ? 
				Tenant::all()
					->paginate($request->paginate)							
			: 	Tenant::all();
				
		if(sizeof($tenants)){
			return response()->json($tenants,200);
		}else{
			
			$message = 'no tenants found';
			
			return response()->json(['message' => $message],401);
		}
	}
	
	public function newTenant(StoreTenant $request){
		
		$tenant = Tenant::create($request->all());
		
		$user = User::create(["email" => $tenant->email,"tenant_id" => $tenant->id, "password" => app('hash')->make($request->password) , "access_level" => 2]);
	
		return response()->json(['data' => $tenant],200);
	}

	//we may not need this
	public function getTenant($tenant){
		try{
			$tenant = Tenant::where('username', $tenant)->first();
			
			return $tenant;
			
		}catch(Exception $e){
			return false;
		}
	}
	
	public function getSettings(Request $request){
		$tenant = Auth::user()->tenant();

		try{
			return response()->json($tenant->meta->settings,200);
		}catch(Exception $e){
			return false;
		}
	}
}
