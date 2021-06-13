<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
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

  public function wards() {
    return $this->hasMany('App\UserGroup','owner_id','id')
      ->where('type_id', 1)
      ->first()
      ->members
      ->load('user');
  }

  public function assignWards(array $student_ids) {
    $students = Student::find($student_ids);

    return $students->map(function($student){
      $group = UserGroup::firstOrCreate([ 
        'owner_id' => $this->id,
        'tenant_id' => $this->tenant->id,
        'type_id' => 1
      ]);

      UserGroupMember::firstOrCreate([
        'group_id' => $group->id,
        'user_id' => $student->id,
      ]);
    });
  }
}
