<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
