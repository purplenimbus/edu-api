<?php

namespace App\Observers;

use App\PaymentProfile;

class PaymentProfileObserver
{
  /**
   * Handle the payment profile "deleting" event.
   *
   * @param  \App\PaymentProfile  $paymentProfile
   * @return void
   */
  public function deleting(PaymentProfile $paymentProfile){
    if ($paymentProfile->items()->count() > 0) {
      $paymentProfile->items()->delete();
    }
  }
}
