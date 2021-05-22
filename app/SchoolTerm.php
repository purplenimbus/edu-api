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
    'tenant_id',
    'type_id',
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
  
  public function status() {
    return $this->belongsTo('App\SchoolTermStatus');
  }

  public function type() {
    return $this->hasOne('App\SchoolTermType', 'id', 'type_id');
  }

  public function registrations() {
    return $this->hasMany('App\Registration', 'term_id');
  }

  public function courses() {
    return $this->hasMany(
      "App\Course",
      "term_id"
    );
  }

  public function getRegisteredStudentsCountAttribute() {
    return $this->registrations->unique('id')->count();
  }

  public function getAssignedInstructorsCountAttribute() {
    return $this->courses()
      ->pluck('instructor_id')
      ->filter(function ($value) { return !is_null($value); })
      ->unique()
      ->count();
  }

  public function getCoursesCompletedAttribute() {
    return $this->registrations()
      ->with('course')
      ->get()
      ->pluck('course.status_id')
      ->every(function ($value) { return $value === 2; });
  }

  public function scopeOfTenant($query, $tenant_id) {
    return $query->whereTenantId($tenant_id);
  }
}
