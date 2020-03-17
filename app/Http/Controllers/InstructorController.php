<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Instructor;
use App\Http\Requests\AssignInstructor;
use App\Http\Requests\StoreInstructor;
use App\Http\Requests\GetInstructors;
use App\Http\Requests\UpdateInstructor;
use Illuminate\Support\Facades\Auth;
use App\Nimbus\NimbusEdu;

class InstructorController extends Controller
{
  /**
   * List all Instructors
   *
   * @return void
   */
  public function index(GetInstructors $request) {
    $tenant_id = Auth::user()->tenant()->first()->id;

    $query = [
      ['tenant_id', '=', $tenant_id]
    ];

    if($request->has('status')){
      array_push($query, [
        'account_status_id',
        '=',
        (int)$nimbus_edu->getStatusID($request->status)->id
      ]);
    }

    $instructors = Instructor::with([
      'status_type'
    ])->where($query);

    if($request->has('paginate')) {
      $instructors = $instructors->paginate($request->paginate);
    }else{
      $instructors = $instructors->get();
    }

    return response()->json($instructors, 200);
  }

  /**
   * Assign Instructor to a Course
   *
   * @return void
   */
  public function assignInstructor(AssignInstructor $request){
    $course = Course::find($request->course_id);

    $course->fill($request->only('instructor_id'));

    $course->save();

    return response()->json($course, 200);
  }

  /**
   * Create an instructor
   *
   * @return void
   */
  public function create(StoreInstructor $request) {
    $tenant = Auth::user()->tenant()->first();

    $nimbus_edu = new NimbusEdu($tenant);

    $student = $nimbus_edu->create_instructor($request);

    $student->load(['status_type:id,name']);

    return response()->json($student, 200);
  }

  /**
   * Edit an instructor
   *
   * @return void
   */
  public function edit(UpdateInstructor $request) {
    $instructor = Instructor::find($request->id);

    $instructor->fill($request->all());
    
    $instructor->save();

    return response()->json($instructor, 200);
  }
}
