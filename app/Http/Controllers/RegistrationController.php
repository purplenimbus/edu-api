<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Registration as Registration;
use App\Http\Requests\GetInstructors;
use App\Http\Requests\UpdateScores;
use App\Http\Requests\DeleteRegistration;
use App\Http\Requests\RegisterStudent;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder as Builder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Nimbus\Enrollment;

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

    $registrations = QueryBuilder::for(Registration::class)
      ->defaultSort('created_at')
      ->allowedSorts(
        'created_at',
        'updated_at'
      )
      ->allowedFilters([
        AllowedFilter::callback('user_id', function (Builder $query, $value) {
            return $query->where('user_id', '=', (int)$value);
        }),
        AllowedFilter::callback('course_id', function (Builder $query, $value) {
            return $query->where('course_id', '=', (int)$value);
        }),
      ])
      ->allowedFields(
        'course.grade',
        'course.instructor.id',
        'course.instructor.name',
        'course.subject',
        'course_score',
        'user.id',
        'user.firstname',
        'user.lastname',
        'user.othernames',
      )
      ->allowedIncludes([
        'course',
        'course.instructor',
        'course_score',
        'course.grade',
        'course.subject',
        'course.status',
        'score',
        'term',
        'user',
      ])
      ->where([
        ['tenant_id', '=', $tenant_id]
      ])
      ->paginate($request->paginate ?? config('edu.pagination'));
    
    return response()->json($registrations, 200);
  }

  /**
   * Register many students in many courses
   *
   * @return void
   */
  public function batch(RegisterStudent $request){
    $tenant = Auth::user()->tenant()->first();

    $enrollmentService = new Enrollment($tenant);

    $registrations = $enrollmentService
      ->enrollStudents($request->student_ids, $request->course_ids);

    return response()->json($registrations, 200);
  }

  /**
   * Update scores
   *
   * @return void
   */
  public function update_scores(UpdateScores $request){
    $registration = Registration::with('score')->find($request->id);

    $registration->score->update($request->only(['scores','comment']));

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
