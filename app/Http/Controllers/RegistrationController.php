<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Registration as Registration;
use App\Http\Requests\GetInstructors;
use App\Http\Requests\UpdateScores;
use App\Http\Requests\DeleteRegistration;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder as Builder;
use Spatie\QueryBuilder\AllowedFilter;

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

    $registrations = QueryBuilder::for(Registration::class)
      ->defaultSort('created_at')
      ->allowedSorts(
        'created_at',
        'updated_at',
      )
      ->allowedFilters([
        AllowedFilter::callback('user_id', function (Builder $query, $value) {
            return $query->where('user_id', '=', (int)$value);
        }),
        AllowedFilter::callback('course_id', function (Builder $query, $value) {
            return $query->where('course_id', '=', (int)$value);
        }),
      ])
      ->allowedIncludes(
        'course','course.grade','course.instructor',
        'course.subject',
        'course.statue',
        'score',
        'term',
        'user',
      )
      ->where([
        ['tenant_id', '=', $tenant_id]
      ])
      ->paginate($request->paginate ?? config('edu.pagination'));
    
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

    $registration->course_score->update($request->only(['scores','comment']));

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
