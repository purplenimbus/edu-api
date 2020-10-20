<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name','description','code','meta','group'
  ];

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
   *  Setup model event hooks
   */
  public static function boot()
  {
    parent::boot();
  }
}
