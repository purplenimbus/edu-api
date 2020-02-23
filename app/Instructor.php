<?php

namespace App;

use App\User;

class Instructor extends User
{
	public function newQuery($excludeDeleted = true)
	{
		return parent::newQuery($excludeDeleted)
		->role('instructor');
	}

	/**
	 *  Setup model event hooks
  */
	public static function boot()
	{
		parent::boot();
		self::creating(function ($model) {
			$model->password = $model->createDefaultPassword();
			$model->assignRole('instructor');
			$status_type = StatusType::where('name', 'created')->first();

			if (!is_null($status_type)) {
				$model->account_status_id = $status_type->id;
			}
		});
	}
}
