<?php

namespace App;

use App\User;

class Instructor extends User
{
  public function newQuery($excludeDeleted = true)
	{
	  return parent::newQuery($excludeDeleted)
	  	->role('teacher');
	}
}
