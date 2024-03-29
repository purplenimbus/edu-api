<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSchoolTerm;
use App\SchoolTerm;
use App\Jobs\CompleteTerm;
use App\Http\Requests\GetTerm;
use App\Http\Requests\UpdateTerm;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder as Builder;

class SchoolTermController extends Controller
{
  /**
   * List terms
   *
   * @return void
   */
  public function index(GetTerm $request)
  {
    $tenant = Auth::user()->tenant()->first();

    $terms = QueryBuilder::for(SchoolTerm::class)
      ->allowedFilters([
        AllowedFilter::callback('status', function (Builder $query, $value) {
          return $query->where('status_id', '=', SchoolTerm::Statuses[$value]);
        }),
        AllowedFilter::callback('status_id', function (Builder $query, $value) {
          return $query->where('status_id', '=', (int)$value);
        }),
      ])
      ->allowedFields([
        'courses',
        'registrations',
      ])
      ->allowedIncludes([
        'courses',
        'registrations',
        'status',
        AllowedInclude::count('coursesCount'),
        AllowedInclude::count('registrationsCount'),
      ])
      ->allowedAppends([
        'assigned_instructors_count',
        'courses_completed',
        'registered_students_count',
      ])
      ->whereTenantId($tenant->id)
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($terms, 200);
  }

  /**
   * View term
   *
   * @return void
   */
  public function show(GetTerm $request)
  {
    $term = QueryBuilder::for(SchoolTerm::class)
      ->allowedAppends([
        'assigned_instructors_count',
        'courses_completed',
        'registered_students_count',
      ])
      ->allowedIncludes([
        'courses',
        'registrations',
        'status',
        AllowedInclude::count('coursesCount'),
        AllowedInclude::count('registrationsCount'),
      ])
      ->whereId($request->id)
      ->first();

    return response()->json($term, 200);
  }

  /**
   * Update term
   *
   * @return void
   */
  public function update(UpdateTerm $request)
  {
    $tenant = Auth::user()->tenant()->first();

    if ($request->status_id === SchoolTerm::Statuses['complete']) {
      CompleteTerm::dispatch($tenant);
    }

    return response()->json([
      'message' => 'your request is being processed'
    ], 200);
  }

  public function create(StoreSchoolTerm $request){
    $tenant = Auth::user()->tenant()->first();
    $data = $request->all();
    $data['tenant_id'] = $tenant->id;
    $term = SchoolTerm::create($data);
    
    return response()->json($term, 200);
  }
}
