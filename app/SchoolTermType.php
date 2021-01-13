<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolTermType extends Model
{
  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'description',
    'name',
    'tenant_id'
  ];

  public function scopeOfTenant($query, $tenant_id) {
    return $query->whereTenantId($tenant_id);
  }
}
