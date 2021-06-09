<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\User as User;
use App\Http\Requests\GetUser as GetUser;
use App\Http\Requests\GetUsers as GetUsers;
use App\Http\Requests\StoreUser as StoreUser;
use App\Http\Requests\StoreBatch as StoreBatch;
use App\Http\Requests\UpdateUser;
use App\Jobs\ProcessBatch;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder as Builder;

class UserController extends Controller
{
  public function index(GetUsers $request)
  {
    $users = QueryBuilder::for(User::class)
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
        AllowedFilter::callback('user_type', function (Builder $query) use ($request) {
          return $query->role($request->value);
        }),
        AllowedFilter::callback('has_image', function (Builder $query, $value) {
          return $value ?
            $query->whereNotNull('image') :
            $query->whereNull('image');
        }),
        AllowedFilter::callback('account_status', function (Builder $query, $value) {
          $query->where(
            'account_status_id',
            '=',
            $value
          );
        }),
      ])
      ->allowedAppends([
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
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($users, 200);
  }

  public function show(GetUser $request)
  {
    $user = QueryBuilder::for(User::class)
      ->allowedAppends([
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
      ->where('id', $request->id)
      ->first();

    return response()->json($user, 200);
  }

  public function update(UpdateUser $request)
  {
    $user = QueryBuilder::for(User::class)
      ->allowedAppends([
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
      ->where('id', $request->id)
      ->first();

    $user->update($request->all());

    return response()->json($user, 200);
  }

  public function create(StoreUser $request)
  {
    $user = User::create($request->all());

    return response()->json($user, 200);
  }

  public function batchUpdate(StoreBatch $request)
  {
    ProcessBatch::dispatch(Auth::user()->tenant()->first(), $request->all()[0], $request->type);

    return response()->json(['message' => 'your request is being processed'], 200);
  }

  public function getAccountStatuses()
  {
    return response()->json(User::StatusTypes, 200);
  }
}
