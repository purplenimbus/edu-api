<?php

namespace App\Http\Controllers;

use App\Guardian;
use App\Http\Requests\DeleteGroupMember;
use App\Http\Requests\UpdateGroup;
use App\Http\Requests\GetGuardian;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\UserGroup;
use App\UserGroupMember;
use Illuminate\Database\Eloquent\Builder;

class WardController extends Controller
{
  /**
   * Display a listing of the guardians wards.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(GetGuardian $request)
  {
    $students = QueryBuilder::for(UserGroupMember::class)
      ->allowedFilters([
        AllowedFilter::partial('firstname', 'user.firstname'),
        AllowedFilter::partial('lastname', 'user.lastname'),
      ])
      ->whereHas('group', function(Builder $query) use ($request) {
        $query->whereOwnerId($request->id);
      })
      ->paginate($request->paginate ?? config('edu.pagination'));
    
    return response()->json($students, 200);
  }
}
