<?php

namespace App;

use App\User;

class Student extends User
{
  public function newQuery($excludeDeleted = true)
	{
	  return parent::newQuery($excludeDeleted)
	  	->role('student');
	}

	public function generateStudentId() {
    return date("Y").sprintf("%04d", $this->id);
  }
}
