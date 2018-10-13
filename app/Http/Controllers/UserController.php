<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User as User;
use App\Http\Requests\StoreUser as StoreUser;
use App\Http\Requests\StoreBatch as StoreBatch;
use App\Jobs\ProcessBatch;
use App\Nimbus\NimbusEdu;

class UserController extends Controller
{
    public function userList($tenant_id , Request $request){

		$nimbus_edu = new NimbusEdu($tenant_id);

		$query = [
			['tenant_id', '=', $tenant_id]
		];
		
		if($request->has('user_type')){
			array_push($query,['user_type_id', '=', $nimbus_edu->getUserType($request->user_type)->id]);
		}

		if($request->has('course_grade_id')){
			array_push($query,['meta->course_grade_id', '=', $request->course_grade_id]);
		}
		
		/*if($request->has('ids')){
			
			//array_push($query,['id', '=', explode(",",$request->ids)]);
			
			$users = 	$request->has('paginate') ? 
				User::where($query)->whereIn('id',explode(",",$request->ids))
					->paginate($request->paginate)
					->only(['id','firstname','lastname','image'])
			: 	User::where($query)->whereIn('id',explode(",",$request->ids))
					->get(['id','firstname','lastname','image']);
		}else{*/
			$users = 	$request->has('paginate') ? 
				User::with(['tenant:id,name','user_type:name,id','account_status:name,id','access_level:name,id'])->where($query)
					->paginate($request->paginate)							
			: 	User::with(['tenant:id,name','user_type:name,id','account_status:name,id','access_level:name,id'])->where($query)
					->get();
		//}
				
		return response()->json($users,200);
		
	}

    public function getUser($tenant_id,$user_id,Request $request){
		
		//validate tenant id ?
		try {
			$user = User::with(['tenant:id,name','user_type:name,id','account_status:name,id','access_level:name,id'])->where([
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

			$user->load(['tenant:id,name','user_type:name,id','account_status:name,id','access_level:name,id']);
					  
			return response()->json($user,200);

		} catch (ModelNotFoundException $ex) {
		  	$message = 'no user id: '.$user_id.' found for tenant : '.$tenant;
			
			return response()->json(['message' => $message],401);
		}
	}

	public function batchUpdate($tenant_id,StoreBatch $request){

		ProcessBatch::dispatch($tenant_id,$request->all()[0],$request->type);

		return response()->json(['message' => 'your request is being processed'],200);
	}
}
