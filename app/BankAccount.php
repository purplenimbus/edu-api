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

  public function getHasOtherBankAccountsAttribute() {
    return !$this->other_bank_accounts->count() == 0;
  }

  public function getOtherBankAccountsAttribute() {
    return BankAccount::whereNotIn('id', [$this->id]);
  }

  public static function boot() 
  {
    parent::boot();

    self::created(function($model) {
      if ($model->tenant->payment_details) {
        $model->tenant->createSubAccount();
      }
    });

    self::saving(function($model) {
      if (!$model->has_other_bank_accounts) {
        $model->default = true;
      }
    });

    self::saved(function($model) {
      if (request()->has('default') && request()->default && $model->has_other_bank_accounts) {
        $model->other_bank_accounts->update([
          'default' => false,
        ]);
      }

      if ($model->tenant->payment_details) {
        $model->tenant->updateOrCreateSubAccount();
      }
    });

    self::deleted(function($model) {
      if ($model->has_other_bank_accounts) {
        $model->other_bank_accounts->first()->update([
          'default' => true,
        ]);
      }
    });
  }
}
