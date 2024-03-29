<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Course;
use App\Curriculum;
use App\Http\Requests\GetCourse;
use App\Http\Requests\GetCourses;
use App\Http\Requests\StoreCourse;
use App\Http\Requests\StoreBatchCourses;
use App\Http\Requests\UpdateCourse;
use App\Jobs\GenerateCourses;
use App\Student;
use App\Http\Requests\DeleteCourse;
use App\Http\Requests\GetNotRegistered;
use App\Http\Requests\UpdateCourseSchema;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder as Builder;
use Spatie\QueryBuilder\AllowedFilter;
use App\NimbusEdu\Syllabus;

class CourseController extends Controller
{
  /**
   * List courses
   *
   * @return void
   */
  public function index(GetCourses $request)
  {
    $user = Auth::user();
    $tenant = $user->tenant()->first();

    $courses = QueryBuilder::for(Course::class)
      ->defaultSort('name')
      ->allowedSorts(
        'name',
        'created_at',
        'updated_at'
      )
      ->allowedFilters([
        'name',
        AllowedFilter::callback('instructor_id', function (Builder $query, $value) {
          return $query->whereInstructorId((int)$value);
        }),
        AllowedFilter::callback('status', function (Builder $query, $value) {
          return $query->whereStatusId(Course::Statuses[$value]);
        }),
        AllowedFilter::callback('status_id', function (Builder $query, $value) {
          return $query->where('status_id', '=', (int)$value);
        }),
        AllowedFilter::callback('student_grade_id', function (Builder $query, $value) {
          return $query->where('student_grade_id', '=', (int)$value);
        }),
        AllowedFilter::callback('has_instructor', function (Builder $query, $value) {
          return $value ?
            $query->whereNotNull('instructor_id') :
            $query->whereNull('instructor_id');
        }),
        'term_id'
      ])
      ->allowedFields([
        'registrations',
        'registrations.user',
        'grade:id,name',
        'instructor:id,firstname,lastname,meta',
        'status'
      ])
      ->allowedIncludes(
        'grade',
        'instructor',
        'registrations',
        'registrations.user',
        'subject'
      )
      ->allowedAppends(
        'status'
      )
      ->whereTenantId($tenant->id)
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($courses, 200);
  }

  /**
   * Update a course
   *
   * @return void
   */
  public function update(UpdateCourse $request)
  {
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
  public function create(StoreCourse $request)
  {
    $tenant = Auth::user()->tenant()->first();

    $data = $request->all();

    $data['tenant_id'] = $tenant->id;

    if (!is_null($tenant->current_term)) {
      $data['term_id'] = $tenant->current_term->id;
    }

    $course = Course::create($data);

    return response()->json($course, 200);
  }

  /**
   * Show the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  App\Http\Requests\GetCourse  $course
   * @return \Illuminate\Http\Response
   */
  public function show(GetCourse $request)
  {
    $tenant_id = Auth::user()->tenant()->first()->id;
    $course = QueryBuilder::for(Course::class)
      ->allowedAppends(
        'status'
      )
      ->allowedFields([
        'registrations',
        'registrations.user',
        'grade:id,name',
        'instructor:id,firstname,lastname,meta',
        'status:id,name'
      ])
      ->allowedIncludes(
        'grade',
        'instructor',
        'registrations',
        'registrations.user',
        'subject'
      )
      ->whereTenantId($tenant_id)
      ->whereId($request->id)
      ->first();

    return response()->json($course, 200);
  }

  /**
   * Batch create courses
   *
   * @return void
   */
  public function batch(StoreBatchCourses $request)
  {
    $syllabus = new Syllabus(Auth::user()->tenant()->first());

    $data = $syllabus->processCourses($request->data);

    return response()->json($data, 200);
  }

  /**
   * Generate courses based on subjects
   *
   * @return void
   */
  public function generate($tenant_id, Request $request)
  {
    GenerateCourses::dispatch(Auth::user()->tenant()->first(), Curriculum::with('grade')->get());

    return response()->json(['message' => 'your request is being processed'], 200);
  }

  /**
   * Unenrolled students
   *
   * @return void
   */
  public function not_registered(GetNotRegistered $request)
  {
    $students = Student::ofUnregistered($request->course_id)->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($students, 200);
  }

  /**
   * Get course statuses
   *
   * @return void
   */
  public function course_statuses()
  {
    return response()->json(Course::Statuses, 200);
  }

  /**
   * Delete courses
   *
   * @return void
   */
  public function delete(DeleteCourse $request)
  {
    Course::destroy($request->course_ids);

    return response()->json(true, 200);
  }
}
