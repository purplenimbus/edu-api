<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentProfileItem extends Model
{
  const Types = [
    'administrative' => 1,
    'tuition' => 2,
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
