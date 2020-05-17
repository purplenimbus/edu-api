<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolTerm extends Model
{
  /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
  protected $fillable = [
    'description',
    'end_date',
    'name',
    'meta',
    'start_date',
    'status_id',
    'tenant_id'
  ];
  
  /**
   * Cast meta property to array
   *
   * @var object
   */

  protected $casts = [
    'end_date' => 'date',
    'meta' => 'object',
    'start_date' => 'date',
  ];

  public function registrations() {
    return $this->hasMany('App\Registration', 'term_id');
  }
}
