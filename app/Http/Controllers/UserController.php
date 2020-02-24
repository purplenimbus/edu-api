<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User as User;
use App\StatusType as StatusType;
use App\Http\Requests\GetUser as GetUser;
use App\Http\Requests\GetUsers as GetUsers;
use App\Http\Requests\StoreUser as StoreUser;
use App\Http\Requests\StoreBatch as StoreBatch;
use App\Jobs\ProcessBatch;
use App\Nimbus\NimbusEdu;

class UserController extends Controller
{
  public function index(GetUsers $request){
    $tenant_id = Auth::user()->tenant()->first()->id;

    $query = [
      ['tenant_id', '=', $tenant_id]
    ];

    if($request->has('course_grade_id')){
      array_push($query,['meta->course_grade_id', '=', (int)$request->course_grade_id]);
    }

    if($request->has('account_status_id')){
      array_push($query,['account_status_id', '=', $request->account_status_id]);
    }

    $users = User::with(['account_status:name,id'])->where($query);

    if($request->has('user_type')) {
      $users = $users->role($request->user_type);
    }

    if($request->has('paginate')) {
      $users = $users->paginate($request->paginate);
    }else{
      $users = $users->get();
    }

    return response()->json($users,200);
  }

  public function getUser(GetUser $request){
    $tenant_id = Auth::user()->tenant()->first()->id;

    $query = [
      ['tenant_id', '=', $tenant_id],
      ['id', '=', $request->user_id]
    ];

    if($request->has('email')){
      array_push($query,['email', '=', $request->email]);
    }

    $user = User::with(['status_type:name,id'])
      ->where($query)->first();

    return response()->json($user, 200);
  }

  public function saveUser(StoreUser $request){
    $user = Auth::user();

    $user->fill($request->all());

    $user->save();

    $user->load(['status_type:name,id']);

    return response()->json($user,200);
  }

  public function batchUpdate(StoreBatch $request){
    ProcessBatch::dispatch(Auth::user()->tenant()->first(), $request->all()[0], $request->type);

    return response()->json(['message' => 'your request is being processed'],200);
  }

  public function getAccountStatuses(){
    return response()->json(StatusType::get(['id','name']),200);
  }
}
