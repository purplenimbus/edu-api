<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
		/**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'account_name',
    'account_number',
    'bank_name',
    'bank_code',
    'description',
    'default',
    'tenant_id',
	];
	
	public function tenant(){
    return $this->belongsTo('App\Tenant');
  }

  public static function boot() 
  {
    parent::boot();

    self::saved(function($model) {
      $other_bank_accounts = BankAccount::whereNotIn('id', [$model->id]);

      if (request()->has('default') && request()->default && $other_bank_accounts->count() > 0) {
        $other_bank_accounts->update([
          'default' => false,
        ]);
      }
    });
  }
}
