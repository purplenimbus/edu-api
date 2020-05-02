<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User as User;
use App\StatusType as StatusType;
use App\Http\Requests\GetUser as GetUser;
use App\Http\Requests\GetUsers as GetUsers;
use App\Http\Requests\StoreUser as StoreUser;
use App\Http\Requests\StoreBatch as StoreBatch;
use App\Jobs\ProcessBatch;
use App\Nimbus\NimbusEdu;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder as Builder;

class UserController extends Controller
{
  public function index(GetUsers $request) {
    $tenant = Auth::user()->tenant()->first();

    $nimbus_edu = new NimbusEdu($tenant);

    $users = QueryBuilder::for(User::class)
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
        AllowedFilter::callback('user_type', function (Builder $query, $value) {
            return $query->role($request->value);
        }),
        AllowedFilter::callback('has_image', function (Builder $query, $value) {
            return $value ?
              $query->whereNotNull('image') :
              $query->whereNull('image');
        }),
        AllowedFilter::callback('course_grade_id', function (Builder $query, $value) {
            $query->where(
              'meta->course_grade_id',
              '=',
              (int)$value
            );
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
        'ref_id'
      ])
      ->allowedIncludes(
        'status',
      )
      ->where([
        ['tenant_id', '=', $tenant->id]
      ])
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($users, 200);
  }

  public function getUser(GetUser $request){
    $tenant_id = Auth::user()->tenant()->first()->id;

    $query = [
      ['tenant_id', '=', $tenant_id],
      ['id', '=', $request->user_id]
    ];

    if($request->has('email')){
      array_push($query,['email', '=', $request->email]);
    }

    $user = User::with(['status_type:name,id'])
      ->where($query)->first();

    return response()->json($user, 200);
  }

  public function saveUser(StoreUser $request){
    $user = Auth::user();

    $user->fill($request->all());

    $user->save();

    $user->load(['status_type:name,id']);

    return response()->json($user,200);
  }

  public function batchUpdate(StoreBatch $request){
    ProcessBatch::dispatch(Auth::user()->tenant()->first(), $request->all()[0], $request->type);

    return response()->json(['message' => 'your request is being processed'],200);
  }

  public function getAccountStatuses(){
    return response()->json(StatusType::get(['id','name']),200);
  }
}
