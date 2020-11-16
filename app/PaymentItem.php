<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
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
		'amount',
		'payment_profile_id',
		'type_id'
	];

	public function PaymentItemType(){
    return $this->hasOne('App\PaymentItemType');
  }
}
