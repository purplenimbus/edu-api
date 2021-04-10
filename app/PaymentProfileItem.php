<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentProfileItem extends Model
{
  const Administrative = 'administrative';
  const Tuition = 'tuition';
  const Types = [
    self::Administrative,
    self::Tuition,
  ];
  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'description',
    'tenant_id',
    'amount',
    'payment_profile_id',
    'type'
  ];
}
