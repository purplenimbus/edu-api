<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User as User;
use App\Http\Requests\StoreUser as StoreUser;
use App\Http\Requests\StoreBatch as StoreBatch;
use App\Jobs\ProcessBatch;

class UserController extends Controller
{
    public function userList($tenant_id , Request $request){
		
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
					->only(['id','firstname','lastname','image'])
			: 	User::where($query)->whereIn('id',explode(",",$request->ids))
					->get(['id','firstname','lastname','image']);
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

    public function getUsers($tenant_id,$user_id,Request $request){
		
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
	
	public function saveUser($tenant_id,$user_id,StoreUser $request){
			
		try {
			$user = User::find($user_id);
		  
		  	//var_dump($user->firstname);
			$data =$request->all();

			//var_dump($request->all());

			unset($data['tenant']);
			unset($data['email']);
			
			$user->fill($data);

			$user->save($data);

			$user->load('tenant');
					  
			return response()->json($user,200);

		} catch (ModelNotFoundException $ex) {
		  	$message = 'no user id: '.$user_id.' found for tenant : '.$tenant;
			
			return response()->json(['message' => $message],401)->setCallback($request->input('callback'));
		}
	}

	public function batchUpdate($tenant_id,StoreBatch $request){

		ProcessBatch::dispatch($tenant_id,$request->all()[0],$request->type);

		return response()->json(['message' => 'your request is being processed'],200);
	}
}
