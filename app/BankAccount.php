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
}
