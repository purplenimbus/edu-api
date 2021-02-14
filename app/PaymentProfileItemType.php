<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentProfileItemType extends Model
{
  const ADMIN = 'administrative';
  const TUITION = 'administrative';
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
  
  public function scopeOfTenant($query, $tenant_id) {
    return $query->whereTenantId($tenant_id);
  }
}
