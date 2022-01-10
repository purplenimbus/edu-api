<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseScore extends Model
{
  use SoftDeletes;
  
  /**
   * The accessors to append to the model"s array form.
   *
   * @var array
   */
  protected $appends = [];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
  	"registration_id",
    "scores",
    "comment"
  ];

  /**
   * The attributes excluded from the model"s JSON form.
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
    "scores" => "array",
  ];

  /**
   *  Get course grade type
  */
  public function getGradeAttribute()
  {
    if (is_null($this->scores)) {
      return;
    } 

    return [];
  }
}
