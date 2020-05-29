<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\UserGroup;
use App\UserGroupMember;

class Guardian extends User
{
  public function newQuery($excludeDeleted = true)
  {
    return parent::newQuery($excludeDeleted)
      ->whereIs('guardian');
  }

  public function getWardsAttribute() {
    return UserGroup::where([
    	['owner_id', $this->id],
    	['type_id', 1],
    ])
    ->first()
    ->members()
    ->get();
  }

  public function assignWard(Student $student) {
    $group = UserGroup::firstOrCreate([ 
      'owner_id' => $this->id,
      'tenant_id' => $this->tenant->id,
      'type_id' => 1
    ]);

    UserGroupMember::firstOrCreate([
      'group_id' => $group->id,
      'user_id' => $student->id,
    ]);

    return $group;
  }

  /**
   *  Setup model event hooks
  */
  public static function boot()
  {
    parent::boot();
    self::creating(function ($model) {
      $model->password = $model->createDefaultPassword();   
    });

    self::created(function ($model) {
      $model->assign('guardian');     
    });
  }
}
