<?php

namespace App;

use App\Notifications\ActivateTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\SchoolTerm;
use App\User;
use Illuminate\Validation\ValidationException;

class Tenant extends Model
{
  use Notifiable;

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'address', 'name',
  ];

  /**
  * Cast meta property to array
  *
  * @var object
  */

  protected $casts = [
    'address' => 'object',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = [
    'current_term'
  ];

  /**
  * The attributes excluded from the model's JSON form.
  *
  * @var array
  */
  protected $hidden = [
    'created_at', 'updated_at'
  ];

  /**
  *  Setup model event hooks
  */
  public static function boot()
  {
    parent::boot();
  }

  public function getCurrentTermAttribute() {
    return SchoolTerm::where([
      'tenant_id' => $this->id,
      'status_id' => 1
    ])
    ->first();
  }

  public function getOwnerAttribute() {
    return User::whereIs('admin')
      ->where('tenant_id', $this->id)
      ->first();
  }

  public function setOwner(User $user) {
    if ($this->owner) {
      throw new ValidationException(__("validation.custom.admin.exists"));
    }

    $user->assign('admin');
  }
}
