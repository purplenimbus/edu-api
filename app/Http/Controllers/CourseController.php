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
use App\Registration;
use App\Student;
use App\CourseStatus;
use App\Http\Requests\GetNotRegistered;
use App\Http\Requests\RegisterStudent;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder as Builder;
use Spatie\QueryBuilder\AllowedFilter;

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

    // $relationships = [
    //   'registrations',
    //   'registrations.user',
    //   'grade:id,name',
    //   'instructor:id,firstname,lastname,meta',
    //   'status:id,name'
    // ];

    $courses = QueryBuilder::for(Course::class)
      ->defaultSort('name')
      ->allowedSorts(
        'name',
        'created_at',
        'updated_at',
      )
      ->allowedFilters([
        'course_grade_id',
        'instructor_id',
        'name',
        'status_id',
        AllowedFilter::callback('course_id', function (Builder $query, $value) {
            return $query->where('id', $value);
        }),
        AllowedFilter::callback('has_instructor', function (Builder $query, $value) {
            return $value ?
              $query->whereNotNull('instructor_id') :
              $query->whereNull('instructor_id');
        }),
      ])
      ->allowedFields([])
      ->allowedIncludes(
        'grade',
        'instructor',
        'registrations',
        'status',
      )
      ->where([
        ['tenant_id', '=', $tenant_id]
      ])
      ->paginate($request->paginate ?? config('edu.pagination'));
    
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

  /**
   * Register students
   *
   * @return void
   */
  public function register_students(RegisterStudent $request){
    $tenant_id = Auth::user()->tenant()->first()->id;
    $registrations = [];
    foreach ($request->student_ids as $id) {
      array_push( $registrations, Registration::updateOrCreate(
        [
          'course_id' => $request->course_id,
          'user_id' => $id,
          'tenant_id' => $tenant_id,
        ]
      ));
    }

    return response()->json($registrations, 200);
  }

  /**
   * Get course statuses
   *
   * @return void
   */
  public function course_statuses(){
    return response()->json(CourseStatus::all(), 200);
  }
}
