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
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder as Builder;

class InstructorController extends Controller
{
  /**
   * List all Instructors
   *
   * @return void
   */
  public function index(GetInstructors $request) {
    $tenant = Auth::user()->tenant()->first();

    $nimbus_edu = new NimbusEdu($tenant);

    $instructors = QueryBuilder::for(Instructor::class)
      ->defaultSort('firstname')
      ->allowedSorts(
        'created_at',
        'date_of_birth',
        'firstname',
        'id',
        'lastname',
        'ref_id',
        'updated_at',
      )
      ->allowedFilters([
        'firstname',
        'email',
        'lastname',
        'ref_id',
        AllowedFilter::callback('has_image', function (Builder $query, $value) {
            return $value ?
              $query->whereNotNull('image') :
              $query->whereNull('image');
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
        'type'
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
      ])
      ->allowedIncludes(
        'status_type',
      )
      ->where([
        ['tenant_id', '=', $tenant->id]
      ])
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($instructors, 200);
  }

  /**
   * Assign Instructor to a Course
   *
   * @return void
   */
  public function assignInstructor(AssignInstructor $request){
    $course = Course::find($request->course_id);

    $instructor = Instructor::find($request->instructor_id);

    return response()->json(
      $instructor->assignInstructor($course),
      200
    );
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
