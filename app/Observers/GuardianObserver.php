<?php

namespace App\Observers;

use App\Guardian;
use App\User;

class GuardianObserver
{
  /**
   * Handle the guardian "created" event.
   *
   * @param  \App\Guardian  $guardian
   * @return void
   */
  public function created(Guardian $guardian)
  {
    $user = User::find($guardian->id);
    $user->assign('guardian');//Assign user model a role to return roles and permissions for JWT Claims
    $guardian->assign('guardian');
  }

  /**
   * Handle the guardian "updated" event.
   *
   * @param  \App\Guardian  $guardian
   * @return void
   */
  public function updated(Guardian $guardian)
  {
      //
  }

  /**
   * Handle the guardian "deleted" event.
   *
   * @param  \App\Guardian  $guardian
   * @return void
   */
  public function deleted(Guardian $guardian)
  {
      //
  }

  /**
   * Handle the guardian "restored" event.
   *
   * @param  \App\Guardian  $guardian
   * @return void
   */
  public function restored(Guardian $guardian)
  {
      //
  }

  /**
   * Handle the guardian "force deleted" event.
   *
   * @param  \App\Guardian  $guardian
   * @return void
   */
  public function forceDeleted(Guardian $guardian)
  {
      //
  }
}
