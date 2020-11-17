<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentProfile extends Model
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
	];

	public function PaymentItems(){
    return $this->hasMany('App\PaymentItem');
  }
}
