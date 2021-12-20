<?php

namespace App;

use App\User;
use App\UserGroup;
use App\UserGroupMember;
use App\Student;

class Guardian extends User
{
  public function newQuery($excludeDeleted = true)
  {
    return parent::newQuery($excludeDeleted)
      ->whereIs('guardian');
  }

  public function getWardsAttribute() {
    return $this->hasMany('App\UserGroup','owner_id','id')
      ->where('type_id', UserGroup::Types['wards'])
      ->first()
      ->members
      ->pluck('user');
  }

  public function assignWards(array $student_ids) {
    $students = Student::find($student_ids);

    return $students->map(function($student){
      $group = UserGroup::firstOrCreate([ 
        'owner_id' => $this->id,
        'tenant_id' => $this->tenant->id,
        'type_id' => UserGroup::Types['wards']
      ]);

      UserGroupMember::firstOrCreate([
        'group_id' => $group->id,
        'user_id' => $student->id,
      ]);
    });
  }
}
