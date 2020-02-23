<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course as Course;
use App\Instructor;
use App\Http\Requests\AssignInstructor as AssignInstructor;

class InstructorController extends Controller
{
  /**
     * List all Instructors
     *
     * @return void
     */
  public function index(Request $request) {
    $tenant_id = Auth::user()->tenant()->first()->id;

    $query = [
      ['tenant_id', '=', $tenant_id]
    ];

    $instructors = Instructor::with([
      'account_status:name,id'
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
}
