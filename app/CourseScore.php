<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseScore extends Model
{
   /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
  	'registration_id',
    'scores'
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [];

	/**
   * Cast meta property to array
   *
   * @var array
   */

	protected $casts = [
    'scores' => 'array',
  ];
}
