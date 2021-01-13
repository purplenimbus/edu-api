<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentProfile extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'description',
    'tenant_id',
    'term_id',
    'course_grade_id',
  ];

  public function items()
  {
    return $this->hasMany('App\PaymentProfileItem');
  }
  
  public function course_grade()
  {
    return $this->belongsTo('App\CourseGrade');
  }
  
  public function term()
  {
    return $this->belongsTo('App\SchoolTerm');
	}
	
	public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }

  public function getTotalAttribute() {
    return $this->items->pluck('amount')->sum();
  }
}
