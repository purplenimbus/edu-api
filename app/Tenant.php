<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\SchoolTerm;
use App\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Wisdomanthoni\Cashier\Billable;

class Tenant extends Model
{
  use Notifiable, Billable;

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

  public function activateSubscription() {
    try {
      $this->createAsPaystackCustomer([
        "email" => $this->owner->email,
        "first_name" => $this->owner->firstname,
        "last_name" => $this->owner->lastname,
      ]);

      $this->newSubscription(env('PAYSTACK_PLAN_NAME'), env('PAYSTACK_PLAN_ID'))
        ->create(null);

    } catch(Exception $e) {
      Log::error('Invalid Request', [
        'message' => $e->getMessage(),
      ]);
    }
  }
}
