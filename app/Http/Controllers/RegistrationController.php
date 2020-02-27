<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Registration as Registration;
use App\Student;
use App\Http\Requests\GetNotRegistered;

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
    $tenant_id = Auth::user()->tenant()->first()->id;

    $query = [
      ['tenant_id', '=', $tenant_id]
    ];

    $relationships = ['course','user','course.grade:name,id,alias','course.instructor:id,firstname,lastname,meta','term:name,year'];
    
    if($request->has('user_id')){
      array_push($query,['user_id', '=', $request->user_id]);
    }

    if($request->has('course_id')){
      array_push($query,['course_id', '=', $request->course_id]);
    }     

    $registrations = $request->has('paginate') ? 
    Registration::with($relationships)->where($query)->paginate($request->paginate) : 
    Registration::with($relationships)->where($query)->get();
    
    return response()->json($registrations, 200);
  }

  /**
   * Register students
   *
   * @return void
   */
  public function registerStudents(Request $request){
    $tenant_id = Auth::user()->tenant()->first()->id;

    RegisterStudents::dispatch($tenant_id, $request->all()[0]); // TO DO, investigate whats going on here , what exactly is the second parameter being used for.

    return response()->json(['message' => 'your request is being processed'], 200);
  }

  /**
   * Unenrolled students
   *
   * @return void
   */
  public function not_registered(GetNotRegistered $request){
    $students = $request->has('paginate') ? 
    Student::ofUnregistered($request->course_id)->paginate($request->paginate) : 
    Student::ofUnregistered($request->course_id)->get();
    
    return response()->json($students, 200);
  }
}
