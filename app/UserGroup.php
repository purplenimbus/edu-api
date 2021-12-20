<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
  	'description',
  	'name',
    'owner_id',
    'type_id',
    'tenant_id',
  ];

  const Types = [
    'wards' => 1,
  ];

  public function members() {
  	return $this->hasMany('App\UserGroupMember', 'group_id');
  }

  public function owner() {
  	return $this->belongsTo('App\User', 'owner_id');
  }

  public function scopeOfGuardians($query, $owner_id = false)
  {
    $params = [
      ['type_id', '=', Self::Types['wards']],
    ];

    if ($owner_id) {
    	array_push($params, ['owner_id', '=', $owner_id]);
    }

    return $query->where($params);
  }
}
