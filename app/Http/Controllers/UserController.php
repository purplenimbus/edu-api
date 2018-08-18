<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User as User;

class UserController extends Controller
{
    public function get($tenant_id,$user_id,Request $request){
		
		//validate tenant id ?
		try {
			$user = User::with('tenant')->where([
					['tenant_id', '=', $tenant_id],
					['id', '=', $user_id],
				])->get();
								
		
			return response()->json($user,200)->setCallback($request->input('callback'));

	 	}catch (ModelNotFoundException $ex) {
		  	$message = 'no user id: '.$user_id.' found for tenant : '.$tenant;
			
			return response()->json(['message' => $message],401);
		}
	}
	
	public function save($tenant,$user_id,Request $request){
		
		$tenant_id = $this->getTenant($tenant);
		$data = $request->all();
		unset($data['id']);
		unset($data['tenant']);
				
		//var_dump($data);
		
		if(isset($tenant_id->id)){
			
			try {
				$user = User::with('tenant')->where([
						['tenant_id', '=', $tenant_id->id],
						['id', '=', $request->id],
					])->first();
			  
				//$user->meta = $request->meta;
				
				$user->update($data);
				
				$user->save();
			  
				return response()->json($user,200);
			} catch (ModelNotFoundException $ex) {
			  	$message = 'no user id: '.$user_id.' found for tenant : '.$tenant;
				
				return response()->json(['message' => $message],401);
			}
		
		}else{
			$message = 'tenant : '.$tenant.' does not exist';
				
			return response()->json(['message' => $message],404);
		}
	}
}
