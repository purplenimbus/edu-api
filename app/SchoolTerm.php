<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolTerm extends Model
{
  const Statuses = [
    'in progress' => 1,
    'complete' => 2,
    'archived' => 3,
  ];
  /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
  protected $fillable = [
    'description',
    'current_term',
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

  protected $appends = [
    'status'
  ];

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

  public function getStatusAttribute() {
    return array_flip(self::Statuses)[$this->status_id];
  }

  public function getRegisteredStudentsCountAttribute() {
    return $this->registrations->unique('user_id')->count();
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
