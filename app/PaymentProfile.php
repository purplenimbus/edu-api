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
    'student_grade_id',
    'term_type_id',
  ];

  public function items()
  {
    return $this->hasMany('App\PaymentProfileItem');
  }
  
  public function course_grade()
  {
    return $this->belongsTo('App\StudentGrade');
  }
  
  public function term_type()
  {
    return $this->belongsTo('App\SchoolTermType', 'term_type_id');
	}
	
	public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }

  public function getTotalAttribute() {
    return $this->items->pluck('amount')->sum();
  }
}
