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
  ];

  public function items()
  {
    return $this->hasMany('App\PaymentProfileItem');
	}
	
	public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }

  public function getTotalAttribute() {
    return $this->items->pluck('amount')->sum();
  }
}
