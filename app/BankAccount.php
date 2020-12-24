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

    // self::created(function($model) {
    //   if ($model->tenant->payment_details) {
    //     $model->tenant->createSubAccount();
    //   }
    // });

    // self::saving(function($model) {
    //   $other_bank_accounts = BankAccount::whereNotIn('id', [$model->id]);

    //   if ($other_bank_accounts->count() == 0) {
    //     $model->default = true;
    //   }
    // });

    // self::saved(function($model) {
    //   // $other_bank_accounts = BankAccount::whereNotIn('id', [$model->id]);

    //   // if (request()->has('default') && request()->default && $other_bank_accounts->count() > 0) {
    //   //   $other_bank_accounts->update([
    //   //     'default' => false,
    //   //   ]);
    //   // }

    //   if ($model->tenant->payment_details) {
    //     $model->tenant->updateOrCreateSubAccount();
    //   }
    // });

    // self::deleted(function($model) {
    //   $other_bank_accounts = BankAccount::whereNotIn('id', [$model->id]);

    //   if ($other_bank_accounts->count() > 0) {
    //     $other_bank_accounts->first()->update([
    //       'default' => true,
    //     ]);
    //   }
    // });
  }
}
