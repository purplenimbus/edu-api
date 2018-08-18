<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\User as User;
use App\Activity as Activity;
use App\Tenant as Tenant;
use App\Transaction as Transaction;
use App\Service as Service;
use App\Http\Requests\StoreTenant as StoreTenant;


class TenantController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('jwt.auth');
    }
	
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
	
	public function users($tenant_id , Request $request){
		
		$query = [
			['tenant_id', '=', $tenant_id]
		];
		
		if($request->has('user_type')){
			array_push($query,['meta->user_type', '=', $request->user_type]);
		}
		
		if($request->has('ids')){
			
			//array_push($query,['id', '=', explode(",",$request->ids)]);
			
			$users = 	$request->has('paginate') ? 
				User::where($query)->whereIn('id',explode(",",$request->ids))
					->paginate($request->paginate)
					->only(['id','fname','lname','image_url'])
			: 	User::where($query)->whereIn('id',explode(",",$request->ids))
					->get(['id','fname','lname','image_url']);
		}else{
			$users = 	$request->has('paginate') ? 
				User::with('tenant')->where($query)
					->paginate($request->paginate)							
			: 	User::with('tenant')->where($query)
					->get();
		}
				
		if(sizeof($users)){
			return response()->json($users,200);
		}else{
			
			$message = 'no users found for tenant : '.$tenant_id;
			
			return response()->json(['message' => $message],401);
		}
		
	}
	
	
	public function getTenant($tenant){
		try{
			$tenant = Tenant::where('username', $tenant)->first();
			
			return $tenant;
			
		}catch(Exception $e){
			return false;
		}
	}
	
}
