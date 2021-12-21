<?php

namespace App\Http\Controllers;

use App\Guardian;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder as Builder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\StoreGuardian;
use App\Http\Requests\GetGuardian;
use App\Http\Requests\DeleteGuardian;
use App\Http\Requests\GetUsers;
use App\Http\Requests\UpdateGuardian;
use App\NimbusEdu\NimbusEdu;

class GuardianController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(GetUsers $request)
  {
    $user = Auth::user();
    $tenant = $user->tenant()->first();

    $guardians = QueryBuilder::for(Guardian::class)
      ->defaultSort('firstname')
      ->allowedSorts(
        'firstname',
        'lastname'
      )
      ->allowedFilters([
        AllowedFilter::partial('firstname'),
        AllowedFilter::partial('lastname'),
        AllowedFilter::partial('email'),
        AllowedFilter::callback('has_image', function (Builder $query, $value) {
          return $value ?
            $query->whereNotNull('image') :
            $query->whereNull('image');
        }),
      ])
      ->allowedAppends(
        'wards'
      )
      ->whereTenantId($tenant->id)
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($guardians, 200);
  }

  /**
   * Show the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Guardian  $guardian
   * @return \Illuminate\Http\Response
   */
  public function show(GetGuardian $request)
  {
    $guardian = QueryBuilder::for(Guardian::class)
      ->allowedAppends([
        'roles',
        'type',
      ])
      ->allowedIncludes(
        'status',
        'wards'
      )
      ->where('id', $request->id)
      ->first();

    return response()->json($guardian, 200);
  }

  /**
   * Create the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Guardian  $guardian
   * @return \Illuminate\Http\Response
   */
  public function create(StoreGuardian $request)
  {
    $tenant = Auth::user()->tenant()->first();

    $nimbus_edu = new NimbusEdu($tenant);

    $guardian = $nimbus_edu->create_guardian($request);

    return response()->json($guardian, 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Guardian  $guardian
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateGuardian $request, $id)
  {
    $guardian = Guardian::find($id);

    $guardian->fill($request->all());

    $guardian->save();

    return response()->json($guardian, 200);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Guardian  $guardian
   * @return \Illuminate\Http\Response
   */
  public function destroy(DeleteGuardian $request)
  {
    Guardian::destroy($request->id);

    return response()->json(true, 200);
  }
}
