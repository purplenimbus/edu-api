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
    if ($bankAccount->tenant->payment_details) {
      $bankAccount->tenant->createSubAccount();
    }
  }

  /**
   * Handle the bank account "saving" event.
   *
   * @param  \App\BankAccount  $bankAccount
   * @return void
   */
  public function saving(BankAccount $bankAccount)
  {
    if (!$bankAccount->has_other_bank_accounts) {
      $bankAccount->default = true;
    }
  }

  /**
   * Handle the bank account "saved" event.
   *
   * @param  \App\BankAccount  $bankAccount
   * @return void
   */
  public function saved(BankAccount $bankAccount)
  {
    if (request()->has('default') && request()->default && $bankAccount->has_other_bank_accounts) {
      $bankAccount->other_bank_accounts->update([
        'default' => false,
      ]);
    }

    if ($bankAccount->tenant->payment_details) {
      $bankAccount->tenant->updateOrCreateSubAccount();
    }
  }

  /**
   * Handle the bank account "deleted" event.
   *
   * @param  \App\BankAccount  $bankAccount
   * @return void
   */
  public function deleted(BankAccount $bankAccount)
  {
    if ($bankAccount->has_other_bank_accounts) {
      $bankAccount->other_bank_accounts->first()->update([
        'default' => true,
      ]);
    }
  }
}
