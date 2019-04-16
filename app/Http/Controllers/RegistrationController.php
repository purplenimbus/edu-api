<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Registration as Registration;
//use App\Http\Requests\StoreBatch as StoreBatch;

use App\Jobs\RegisterStudents;

class RegistrationController extends Controller
{
  /**
   * List registrations
   *
   * @return void
   */
  public function registrations(Request $request)
  {
    $tenant_id = Auth::user()->tenant()->id;

    $query = [
      ['tenant_id', '=', $tenant_id]
    ];

    $relationships = ['course','user','course.grade:name,id,alias','course.instructor:id,firstname,lastname,meta','term:name,year'];
    
    if($request->has('user_id')){
      array_push($query,['user_id', '=', $request->user_id]);
      //array_push($relationships,'user');
    }

    if($request->has('course_id')){
      array_push($query,['course_id', '=', $request->course_id]);
      //array_push($relationships,'course');
    }

    $registrations = $request->has('paginate') ? 
      Registration::with($relationships)->where($query)->paginate($request->paginate) : 
      Registration::with($relationships)->where($query)->get();
        
    if(sizeof($registrations)){
      return response()->json($registrations,200)->setCallback($request->input('callback'));
    }else{
      $message = 'no registrations found for tenant id : '.$tenant_id;

      return response()->json(['message' => $message],204)->setCallback($request->input('callback'));
    }
  }

    /**
     * Batch create subjects
     *
     * @return void
     */
  public function registerStudents(Request $request){
    $tenant_id = Auth::user()->tenant()->id;

    RegisterStudents::dispatch($tenant_id, $request->all()[0]);

    return response()->json(['message' => 'your request is being processed'],200);
  }


}
