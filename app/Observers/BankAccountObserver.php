<?php

namespace App\Observers;

use App\BankAccount;

class BankAccountObserver
{
  /**
   * Handle the bank account "created" event.
   *
   * @param  \App\BankAccount  $bankAccount
   * @return void
   */
  public function created(BankAccount $bankAccount)
  {
    $this->setDefaultBankAccount($bankAccount);
    
    $this->newSubAccount($bankAccount);

    $this->updateOtherBankAccount($bankAccount);
  }

  /**
   * Handle the bank account "updated" event.
   *
   * @param  \App\BankAccount  $bankAccount
   * @return void
   */
  public function updated(BankAccount $bankAccount)
  {
    $this->updateSubAccount($bankAccount);
  }

  /**
   * Handle the bank account "deleted" event.
   *
   * @param  \App\BankAccount  $bankAccount
   * @return void
   */
  public function deleted(BankAccount $bankAccount)
  {
    $this->updateDefaultBankAccount($bankAccount);
  }

  /**
   * Handle the bank account "restored" event.
   *
   * @param  \App\BankAccount  $bankAccount
   * @return void
   */
  public function restored(BankAccount $bankAccount)
  {
      //
  }

  /**
   * Handle the bank account "force deleted" event.
   *
   * @param  \App\BankAccount  $bankAccount
   * @return void
   */
  public function forceDeleted(BankAccount $bankAccount)
  {
      //
  }
  
  private function setDefaultBankAccount(BankAccount $bankAccount){
    $other_bank_accounts = BankAccount::whereNotIn('id', [$bankAccount->id]);

    if ($other_bank_accounts->count() == 0) {
      $bankAccount->default = true;
    }
  }
  
  private function updateOtherBankAccount(BankAccount $bankAccount){
    $other_bank_accounts = BankAccount::whereNotIn('id', [$bankAccount->id]);

    if (request()->has('default') && request()->default && $other_bank_accounts->count() > 0) {
      $other_bank_accounts->update([
        'default' => false,
      ]);
    }
  }

  private function updateSubAccount(BankAccount $bankAccount){
    if ($bankAccount->tenant->payment_details) {
      $bankAccount->tenant->updateOrCreateSubAccount();
    }
  }

  private function newSubAccount(BankAccount $bankAccount){
    if ($bankAccount->tenant->payment_details) {
      $bankAccount->tenant->createSubAccount();
    }
  }

  private function updateDefaultBankAccount(BankAccount $bankAccount){
    $other_bank_accounts = BankAccount::whereNotIn('id', [$bankAccount->id]);

    if ($other_bank_accounts->count() > 0) {
      $other_bank_accounts->first()->update([
        'default' => true,
      ]);
    }
  }
}
