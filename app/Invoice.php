<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cknow\Money\Money;

class Invoice extends Model
{
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
 *  Setup model event hooks
 */
  public static function boot()
  {
    parent::boot();

    self::deleting(function ($model) {
      if ($model->line_items()->count() > 0) {
        $model->line_items()->delete();
      }
    });
  }

  public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }

  public function getBalanceAttribute() {
    return $this->line_items->sum(function($line_item){
      return $line_item->amount*$line_item->quantity;
    });
  }

  public function getFormattedBalanceAttribute() {
    $defaultCurrency = config('money.defaultCurrency', 'NGN');

    $money = Money::$defaultCurrency($this->balance);
    
    $balance = $money->toArray();

    $balance['value'] = intval($money->formatByDecimal());
    
    return $balance;
  }
}
