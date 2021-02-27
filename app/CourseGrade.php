<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseGrade extends Model
{
  public $table = "course_grades";

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
    'name','meta','alias','description','tenant_id'
  ];

  public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }
}
