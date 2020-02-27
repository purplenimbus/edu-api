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
use App\Nimbus\NimbusEdu;
use App\Student;
use App\Http\Requests\GetNotRegistered;

class CourseController extends Controller
{
  /**
   * List courses
   *
   * @return void
   */
  public function index(GetCourses $request)
  {
    $tenant_id = Auth::user()->tenant()->first()->id;

    $query = [
      ['tenant_id', '=', $tenant_id]
    ];

    if($request->has('course_id')){
      $query[] = ['id', '=', $request->course_id];
    } 
    if($request->has('course_grade_id')){
      $query[] = ['course_grade_id', '=', $request->course_grade_id];
    } 
    if($request->has('name')){
      $query[] = ['name', '=', $request->name];
    } 
    if($request->has('instructor_id')){
      $query[] = ['instructor_id', '=', $request->instructor_id];
    }

    $relationships = ['registrations','registrations.user','grade:id,name','instructor:id,firstname,lastname,meta'];
    
    $courses = $request->has('paginate') ? 
    Course::with($relationships)
    ->where($query)
    ->paginate($request->paginate) 

    : Course::with($relationships)
    ->where($query)
    ->get();
    
    return response()->json($courses, 200);   
  }
  
  /**
     * Update a course
     *
     * @return void
     */
  public function update(UpdateCourse $request){
    $tenant_id = Auth::user()->tenant()->first()->id;

    $course = Course::find($request->id);

    $course->fill($request->all());

    $course->save();
    
    return response()->json($course, 200);
  }

  /**
     * Create a new course
     *
     * @return void
     */
  public function create(StoreCourse $request){
    $tenant = Auth::user()->tenant()->first();
    
    $data = $request->all();

    $data['tenant_id'] = $tenant->id;

    $course = Course::create($data);
    
    return response()->json($course, 200);
  }

  /**
   * Batch create subjects
   *
   * @return void
   */
  public function batch(StoreBatch $request){
    ProcessBatch::dispatch(Auth::user()->tenant()->first(), $request->all()[0],$request->type);

    return response()->json(['message' => 'your request is being processed'], 200);
  }

  /**
   * Generate courses based on subjects
   *
   * @return void
   */
  public function generate($tenant_id, Request $request){
    GenerateCourses::dispatch(Auth::user()->tenant()->first(), Curriculum::with('grade')->get());

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
