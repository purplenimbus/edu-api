<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentProfileItem extends Model
{
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
    'type_id'
  ];

  public function type(){
    return $this->hasOne('App\PaymentProfileItemType', 'id', 'type_id');
  }
}
