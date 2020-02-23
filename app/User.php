<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Webpatser\Uuid\Uuid as Uuid;
use Spatie\Permission\Models\Role as Role;

class User extends Authenticatable implements JWTSubject
{
  use HasRoles, Notifiable;

  public $table = "users";

  protected $guard_name = 'api';
	
  protected $keyType = "string";
	
	//protected $primaryKey = 'uuid';

  /**
   * The attributes that should be mutated to dates.
   *
   * @var array
   */
  protected $dates = [
    'date_of_birth',
  ];
	/**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
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
    'ref_id'
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'password','tenant_id','created_at','updated_at','remember_token','account_status_id'
  ];

	/**
   * Cast meta property to array
   *
   * @var array
   */

	protected $casts = [
    'meta' => 'object',
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
    return [
      'user' => $this->only(['email', 'firstname', 'lastname', 'id']),
      'tenant' => $this->tenant()->first(),
      'role' => $this->type,
      'permissions' => $this->roles->first()->permissions()->get(['name']),
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
	 *  Setup model event hooks
  */
	public static function boot()
	{
		parent::boot();
		self::creating(function ($model) {
			$model->uuid = (string) Uuid::generate(4);
		});
	}
  
  /**
   *  Get user type
  */
  public function getTypeAttribute()
  {
    return $this->hasAnyRole(Role::all()) ?
      $this->roles->first()->name : '';
  }

  /**
   *  generate password
  */
  public function createDefaultPassword()
  {
    return app('hash')->make($this->email);
  }

  /**
   * Relationships.
   *
   */
  function tenant(){
    return $this->belongsTo('App\Tenant');
  }

  function status_type(){
    return $this->belongsTo('App\StatusType','account_status_id');
  }
}
