<?php

namespace App\Observers;

use App\User;

class UserObserver
{
  /**
   * Handle the instructor "creating" event.
   *
   * @param  \App\User  $user
   * @return void
   */
  public function creating(User $user)
  {
    $user->account_status_id = User::StatusTypes['created'];
  }
}
