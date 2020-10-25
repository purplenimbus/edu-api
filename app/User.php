<?php

namespace App;

use App\Notifications\PasswordResetSuccess;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
  use Notifiable, HasRolesAndAbilities;

  public $table = "users";

  protected $guard_name = 'api';

  /**
   * The attributes that should be mutated to dates.
   *
   * @var array
   */
  protected $dates = [
    'date_of_birth',
    'email_verified_at',
  ];
	/**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'address',
    'date_of_birth',
    'firstname',
    'lastname',
    'othernames',
    'email',
    'tenant_id',
    'meta',
    'password',
    'image',
    'account_status_id',
    'ref_id',
    'email_verified_at'
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'password','tenant_id','created_at','updated_at','remember_token','account_status_id','meta'
  ];

	/**
   * Cast meta property to array
   *
   * @var array
   */

	protected $casts = [
    'address' => 'object',
    'email_verified_at' => 'datetime',
    'meta' => 'object',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = [
    'fullname',
    'type',
  ];

	/**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
    $permissions = $this->getAbilities()->pluck('name');

    return [
      'user' => $this->toArray(),
      'permissions' => $permissions,
      'tenant' => $this->tenant,
    ];
  }

  /**
   * Set the user's first name.
   *
   * @param  string  $value
   * @return void
   */
  public function setFirstNameAttribute($value)
  {
    $this->attributes['firstname'] = strtolower($value);
  }

  /**
   * Set the user's las name.
   *
   * @param  string  $value
   * @return void
   */
  public function setLastNameAttribute($value)
  {
    $this->attributes['lastname'] = strtolower($value);
  }

  /**
   * Set the user's last name.
   *
   * @param  string  $value
   * @return void
   */
  public function setOtherNamesAttribute($value)
  {
    $this->attributes['othernames'] = strtolower($value);
  }

  /**
   * Set the user's email
   *
   * @param  string  $value
   * @return void
   */
  public function setEmailAttribute($value)
  {
    $this->attributes['email'] = strtolower($value);
  }

  /**
   * Set the user's password
   *
   * @param  string  $value
   * @return void
   */
  public function setPasswordAttribute($value){
    $this->attributes['password'] = Hash::make($value);
  }
  
  /**
   *  Get user type
  */
  public function getTypeAttribute()
  {
    return !is_null($this->roles->first()) ?
      $this->roles->first()->name : '';
  }

  /**
   *  Get full name
  */
  public function getFullnameAttribute()
  {
    return trim("{$this->firstname} {$this->lastname} {$this->othernames}");
  }

  /**
   *  generate password
  */
  public function createDefaultPassword()
  {
    return $this->email;
  }

  public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }
  /**
   * Relationships.
   *
   */
  function tenant(){
    return $this->belongsTo('App\Tenant');
  }

  function status(){
    return $this->belongsTo('App\StatusType','account_status_id');
  }

  public static function boot() 
  {
    parent::boot();

    self::saved(function($model) {
      if (request()->has('password_confirmation') && $model->wasChanged('password')) {
        $model->notify(new PasswordResetSuccess);
      }
    });
  }
}
