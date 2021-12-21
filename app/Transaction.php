<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	/**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
		'amount',
		'authorization_code',
		'paid_at',
		'invoice_id',
		'ref_id',
		'status_id',
		'tenant_id',
	];

	/**
   * The attributes that should be mutated to dates.
   *
   * @var array
   */
  protected $dates = [
    'paid_at',
  ];
	
	public function getPayStackTransaction() {
		if (!$this->ref_id) {
			return null;
		}

		if ($this->ref_id) {
			request()->merge([
				'trxref' => $this->ref_id,
			]);

			return paystack()->getPaymentData();
		}
	}

	public function invoice()
  {
    return $this->belongsTo('App\Invoice');
	}
	
	public function status()
  {
    return $this->hasOne('App\InvoiceStatus');
  }
}
