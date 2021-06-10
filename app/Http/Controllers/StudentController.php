<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Student;
use App\Guardian;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreStudent;
use App\Http\Requests\UpdateStudent;
use App\Http\Requests\GetStudents;
use App\Http\Requests\GetStudent;
use App\Http\Requests\GetTranscript;
use App\NimbusEdu\NimbusEdu;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder as Builder;

class StudentController extends Controller
{
  /**
   * List all Students
   *
   * @return void
   */
  public function index(GetStudents $request) {
    $tenant = Auth::user()->tenant()->first();

    $nimbus_edu = new NimbusEdu($tenant);

    $students = QueryBuilder::for(Student::class)
      ->defaultSort('firstname')
      ->allowedSorts(
        'created_at',
        'date_of_birth',
        'firstname',
        'id',
        'lastname',
        'ref_id',
        'updated_at'
      )
      ->allowedFilters([
        AllowedFilter::partial('firstname'),
        'email',
        AllowedFilter::partial('lastname'),
        'ref_id',
        AllowedFilter::callback('has_image', function (Builder $query, $value) {
            return $value ?
              $query->whereNotNull('image') :
              $query->whereNull('image');
        }),
        AllowedFilter::callback('student_grade_id', function (Builder $query, $value) {
            $query->where(
              'meta->student_grade_id',
              '=',
              (int)$value
            );
        }),
        AllowedFilter::callback('status', function (Builder $query, $value) use ($nimbus_edu) {
            $query->where(
              'account_status_id',
              '=',
              (int)$nimbus_edu->getStatusID($value)->id
            );
        }),
      ])
      ->allowedAppends([
        'grade',
        'guardian',
        'type',
        'status'
      ])
      ->allowedFields([
        'address',
        'date_of_birth',
        'firstname',
        'lastname',
        'othernames',
        'email',
        'meta',
        'password',
        'image',
        'ref_id'
      ])
      ->where([
        ['tenant_id', '=', $tenant->id]
      ])
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($students, 200);
  }

  /**
   * Create a student
   *
   * @return void
   */
  public function create(StoreStudent $request) {
    $tenant = Auth::user()->tenant()->first();

    $nimbus_edu = new NimbusEdu($tenant);

    $student = $nimbus_edu->create_student($request);

    return response()->json($student, 200);
  }

  /**
   * Show the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Guardian  $guardian
   * @return \Illuminate\Http\Response
   */
  public function show(GetStudent $request)
  {
    $tenant_id = Auth::user()->tenant()->first()->id;

    $student = QueryBuilder::for(Student::class)
      ->allowedAppends([
        'roles',
        'type',
      ])
      ->allowedFields([
        'address',
        'date_of_birth',
        'firstname',
        'lastname',
        'othernames',
        'email',
        'meta',
        'password',
        'image',
        'ref_id',
        'wards.members',
        'roles'
      ])
      ->allowedIncludes(
        'status'
      )
      ->where('id', $request->id)
      ->first();

    return response()->json($student, 200);
  }

  /**
   * Edit a student
   *
   * @return void
   */
  public function edit(UpdateStudent $request) {
    $student = Student::find($request->id);

    $student->fill($request->all());
    
    $student->save();

    if ($request->has('guardian_id')) {
      $guardian_id = request()->guardian_id;
      $guardian = Guardian::find($guardian_id);

      if ($guardian) {
        $guardian->assignWards([$student->id]);
      }
    }

    return response()->json($student, 200);
  }

  /**
   * Get a students transcripts
   *
   * @return void
   */
  public function transcripts(GetTranscript $request) {
    $transcripts = Student::find($request->id)->getTranscripts();

    return response()->json($transcripts, 200);
  }

  /**
   * Get a students eligible courses they are not registered in
   *
   * @return void
   */
  public function valid_courses(GetStudent $request) {
    $student = Student::find($request->id);

    $courses = QueryBuilder::for(Course::class)
      ->validCourses($student)
      ->defaultSort('name')
      ->allowedSorts(
        'created_at',
        'id',
        'name',
        'updated_at'
      )
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($courses, 200);
  }
}
