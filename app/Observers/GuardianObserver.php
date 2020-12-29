<?php

namespace App\Observers;

use App\Guardian;
use App\User;

class GuardianObserver
{
  /**
   * Handle the guardian "creating" event.
   *
   * @param  \App\Guardian  $guardian
   * @return void
   */
  public function creating(Guardian $guardian)
  {
    $guardian->password = $guardian->createDefaultPassword(); 
  }

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
}
