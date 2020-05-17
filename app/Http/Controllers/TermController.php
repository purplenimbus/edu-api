<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\SchoolTerm;
use App\SchoolTermStatus;
use App\Jobs\CompleteTerm;
use App\Http\Requests\GetTenant;
use App\Http\Requests\GetTerm;
use App\Http\Requests\UpdateTerm;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder as Builder;

class TermController extends Controller
{
  /**
   * List terms
   *
   * @return void
   */
  public function index(GetTerm $request){
    $tenant = Auth::user()->tenant()->first();

    $terms = QueryBuilder::for(SchoolTerm::class)
      ->allowedFilters([
        AllowedFilter::callback('status', function (Builder $query, $value) {
            $status = SchoolTermStatus::where('name', $value)->first();

            return $query->where('status_id', '=', isset($status->id) ? (int)$status->id: false);
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
        AllowedInclude::count('instructorsCount'),
        AllowedInclude::count('studentsCount')
      ])
      ->where([
        ['tenant_id', '=', $tenant->id]
      ])
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($terms, 200);
  }

  /**
   * View term
   *
   * @return void
   */
  public function show(GetTerm $request){
    $tenant = Auth::user()->tenant()->first();

    $term = SchoolTerm::find($request->id);

    return response()->json($term, 200);
  }

  /**
   * Update term
   *
   * @return void
   */
  public function update(UpdateTerm $request){
    $tenant = Auth::user()->tenant()->first();

    if ($request->status_id === 2) {
      CompleteTerm::dispatch($tenant);
    }

    return response()->json([
      'message' => 'your request is being processed'
    ], 200);
  }
}
