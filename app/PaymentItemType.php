<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentItemType extends Model
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
}
