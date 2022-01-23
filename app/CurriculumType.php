<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurriculumType extends Model
{
  public $table = "curricula_types";

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'created_at','updated_at'
  ];

  /**
   * Cast meta property to array
   *
   * @var array
   */
 
  protected $casts = [
    'meta' => 'array',
  ];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'description','meta','country'
  ];
}
