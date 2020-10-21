<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'details',
    'due_date',
    'comments',
    'invoice_number',
    'status_id',
    'tenant_id',
    'term_id',
    'recipient_id',
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'status_id',
    'tenant_id'
  ];

  public function registrations(){
    return $this->hasMany('App\Registration');
  }

  public function line_items(){
    return $this->hasMany('App\LineItem');
  }

  public function status(){
    return $this->belongsTo('App\InvoiceStatus','status_id');
  }

  public function tenant(){
    return $this->belongsTo('App\Tenant');
  }

  public function recipient(){
    return $this->belongsTo('App\User','recipient_id');
  }

  /**
   * Cast meta property to array
   *
   * @var array
   */
  protected $casts = [
    'meta' => 'array',
  ];

  /**
 *  Setup model event hooks
 */
  public static function boot()
  {
    parent::boot();
  }

  public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }
}
