<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User as User;
use App\Http\Requests\GetUser as GetUser;
use App\Http\Requests\StoreUser as StoreUser;
use App\Http\Requests\StoreBatch as StoreBatch;
use App\Jobs\ProcessBatch;
use App\Nimbus\NimbusEdu;

class UserController extends Controller
{
  public function userList(Request $request){
    $tenant = Auth::user()->tenant();

    $tenant_id = $tenant->id;

    $nimbus_edu = new NimbusEdu($tenant);

    $query = [
      ['tenant_id', '=', $tenant_id]
    ];
    
    if($request->has('user_type')){
      array_push($query,['user_type_id', '=', $nimbus_edu->getUserType($request->user_type)->id]);
    }

    if($request->has('course_grade_id')){
      array_push($query,['meta->course_grade_id', '=', $request->course_grade_id]);
    }
    
    $users =  $request->has('paginate') ? 
    User::with(['tenant:id,name','user_type:name,id','account_status:name,id','access_level:name,id'])->where($query)
    ->paginate($request->paginate)              
    :   User::with(['tenant:id,name','user_type:name,id','account_status:name,id','access_level:name,id'])->where($query)
    ->get();

    return response()->json($users,200);

  }

  public function getUser(GetUser $request){
    $tenant_id = Auth::user()->tenant()->id;

    $user = User::with(['tenant:id,name','user_type:name,id','account_status:name,id','access_level:name,id'])->where([
      ['tenant_id', '=', $tenant_id],
      ['id', '=', $request->user_id],
    ])->get();

    return response()->json($user,200);
  }

  public function saveUser(StoreUser $request){
    $user = User::find($user_id);

    $user->fill($request->all());

    $user->save($data);

    $user->load(['tenant:id,name','user_type:name,id','account_status:name,id','access_level:name,id']);

    return response()->json($user,200);
  }

  public function batchUpdate($tenant_id,StoreBatch $request){
    ProcessBatch::dispatch(Auth::user()->tenant(), $request->all()[0], $request->type);

    return response()->json(['message' => 'your request is being processed'],200);
  }
}
