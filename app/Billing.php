<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'details','status_id','tenant_id','term_id'
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'status_id',
    'tenant_id'
  ];

  public function registrations(){
    return $this->hasMany('App\Registration');
  }

  public function status(){
    return $this->belongsTo('App\BillingStatus','status_id');
  }

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
