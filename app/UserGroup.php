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
  ];

  public function members() {
  	return $this->hasMany('App\UserGroupMember', 'group_id');
  }

  public function owner() {
  	return $this->belongsTo('App\User', 'owner_id');
  }
}
