<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Course as Course;
use App\Curriculum as Curriculum;
use App\Http\Requests\GetCourses as GetCourses;
use App\Http\Requests\StoreCourse as StoreCourse;
use App\Http\Requests\UpdateCourse as UpdateCourse;
use App\Http\Requests\StoreBatch as StoreBatch;
use App\Jobs\ProcessBatch;
use App\Jobs\GenerateCourses;


class CourseController extends Controller
{
  /**
   * List courses
   *
   * @return void
   */
  public function getCourses(GetCourses $request)
  {
    $tenant_id = Auth::user()->tenant()->first()->id;

    $query = [
      ['tenant_id', '=', $tenant_id]
    ];

    $query[] = ['id', '=', $request->course_id];

    $query[] = ['course_grade_id', '=', $request->course_grade_id];

    $query[] = ['name', '=', $request->name];

    $query[] = ['instructor_id', '=', $request->instructor_id];

    $relationships = ['registrations','registrations.user','grade:id,name','instructor:id,firstname,lastname,meta'];
    
    $courses = $request->has('paginate') ? 
    Course::with($relationships)
    ->where($query)
    ->paginate($request->paginate) 

    : Course::with($relationships)
    ->where($query)
    ->get();
    
    return response()->json(['message' => $message],204);   
  }
  
  /**
     * Create a new course
     *
     * @return void
     */
  public function updateCourse(UpdateCourse $request){
    $tenant_id = Auth::user()->tenant()->first()->id;

    $course = Course::where('tenant_id',$tenant_id)
    ->where('id',$request->id)
    ->first();

    $data = $request->all();

    unset($data['id']);

    $course->fill($data);

    $course->save();
    
    return response()->json($course,200);
  }

  /**
     * Create a new course
     *
     * @return void
     */
  public function createCourse(StoreCourse $request){
    $tenant_id = Auth::user()->tenant()->first()->id;

    dd($request->all());

    //$data = $request->all();
    
    /*$data['tenant_id'] = $tenant_id;

    $data['code'] = $data['class']['name']
    
    $course = Course::create($data);
    
    return response()->json($course,200)->setCallback($request->input('callback'));*/
  }

  /**
   * Batch create subjects
   *
   * @return void
   */
  public function batchUpdate(StoreBatch $request){
    ProcessBatch::dispatch(Auth::user()->tenant()->first(), $request->all()[0],$request->type);

    return response()->json(['message' => 'your request is being processed'], 200);
  }

  /**
   * Generate courses based on subjects
   *
   * @return void
   */
  public function generateCourses($tenant_id,Request $request){
    GenerateCourses::dispatch(Auth::user()->tenant()->first(), Curriculum::with('grade')->get());

    return response()->json(['message' => 'your request is being processed'], 200);
  }
}
