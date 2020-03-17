<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Registration as Registration;
use App\Http\Requests\GetInstructors;
use App\Http\Requests\UpdateScores;
use App\Http\Requests\DeleteRegistration;

class RegistrationController extends Controller
{
  /**
   * List registrations
   *
   * @return void
   */
  public function index(Request $request)
  {
    $tenant_id = Auth::user()->tenant()->first()->id;

    $query = [
      ['tenant_id', '=', $tenant_id]
    ];

    $relationships = ['course','user','course.grade:name,id,alias','course.instructor:id,firstname,lastname,meta','term:name,year','course_score'];
    
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
   * Update scores
   *
   * @return void
   */
  public function update_scores(UpdateScores $request){
    $registration = Registration::with('course_score')->find($request->id);

    $registration->course_score->update($request->only('scores'));

    return response()->json($registration, 200);
  }

  /**
   * Delete registrations
   *
   * @return void
   */
  public function delete(DeleteRegistration $request){
    Registration::destroy($request->registration_ids);

    return response()->json(true, 200);
  }
}
