<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserGroupMember extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'group_id',
    'user_id',
  ];

  public function user() {
  	return $this->belongsTo('App\Student', 'user_id');
  }

  public function group() {
  	return $this->belongsTo('App\UserGroup');
  }
}
