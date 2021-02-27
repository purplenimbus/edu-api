<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DeleteGroupMember;
use App\Http\Requests\UpdateGroup;
use App\Http\Requests\GetGuardian;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use App\UserGroup;

class WardController extends Controller
{
    /**
   * Display a listing of the guardians wards.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(GetGuardian $request)
  {
    $tenant_id = Auth::user()->tenant()->first()->id;

    $students = QueryBuilder::for(UserGroup::class)
      ->allowedFilters([
        AllowedFilter::partial('firstname', 'members.user.firstname'),
        AllowedFilter::partial('lastname', 'members.user.lastname'),
        AllowedFilter::partial('student_grade_id', 'members.user.meta'),
      ])
      ->allowedIncludes(
        'members.user',
        'members.user.status',
        'owner'
      )
      ->OfGuardians($request->id)
      ->where('tenant_id', $tenant_id)
      ->paginate($request->paginate ?? config('edu.pagination'));
    
    return response()->json($students, 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Guardian  $guardian
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateGroup $request)
  {
    $tenant_id = Auth::user()->tenant()->first()->id;

    $group = UserGroup::OfGuardians($request->id)
      ->where([
        ['tenant_id', '=', $tenant_id],
      ]);

    return response()->json($group, 200);
  }

  /**
   * Remove the relationships between a guardian and the specified wards
   *
   * @param  \App\Guardian  $guardian
   * @return \Illuminate\Http\Response
   */
  public function destroy(DeleteGroupMember $request)
  {
    UserGroupMember::destroy($request->member_ids);

    return response()->json(true, 200);
  }
}
