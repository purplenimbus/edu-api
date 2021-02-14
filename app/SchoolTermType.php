<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolTermType extends Model
{
  const FIRST = "first term";
  const SECOND = "second term";
  const THIRD = "third term";
  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'description',
    'end_date',
    'name',
    'start_date',
    'tenant_id'
  ];

  public function scopeOfTenant($query, $tenant_id) {
    return $query->whereTenantId($tenant_id);
  }

  public function scopeOfTermTypeName($query, $name) {
    return $query->whereName($name);
  }
}
