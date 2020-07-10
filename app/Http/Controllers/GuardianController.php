<?php

namespace App\Http\Controllers;

use App\Guardian;
use App\UserGroup;
use App\Student;
use App\Nimbus\NimbusEdu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder as Builder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\StoreGuardian;
use App\Http\Requests\GetGuardian;

class GuardianController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $tenant_id = Auth::user()->tenant()->first()->id;

    $guardians = QueryBuilder::for(Guardian::class)
      ->allowedAppends([
        'roles',
        'type',
        'wards.members.user',
      ])
      ->allowedFilters([
        AllowedFilter::partial('firstname'),
        AllowedFilter::partial('lastname'),
        'email',
        AllowedFilter::callback('has_image', function (Builder $query, $value) {
            return $value ?
              $query->whereNotNull('image') :
              $query->whereNull('image');
        }),
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
        'wards.members'
      ])
      ->allowedIncludes(
        'status',
      )
      ->where('tenant_id', $tenant_id)
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
    $tenant_id = Auth::user()->tenant()->first()->id;

    $guardian = QueryBuilder::for(Guardian::class)
      ->allowedAppends([
        'roles',
        'type',
        'wards.members.user',
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
        'wards.members'
      ])
      ->allowedIncludes(
        'status',
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
  public function update(StoreGuardian $request, $id)
  {
    $tenant_id = Auth::user()->tenant()->first()->id;

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
