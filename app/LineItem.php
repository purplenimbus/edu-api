<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cknow\Money\Money;

class LineItem extends Model
{
	/**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
		'amount',
		'description',
		'invoice_id',
    'quantity',
    'tenant_id',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = [
    'formatted_amount'
  ];

  public function setAmountAttribute ($value) {
    $this->attributes['amount'] = $value*100; // always convert to the smallest denomination
  }

  public function getFormattedAmountAttribute() {
    $defaultCurrency = config('money.defaultCurrency', 'NGN');

    $money = Money::$defaultCurrency($this->amount);
    
    $balance = $money->toArray();

    $balance['value'] = intval($money->formatByDecimal());

    return $balance;
  }
}
