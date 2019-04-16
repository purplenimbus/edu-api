<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Webpatser\Uuid\Uuid as Uuid;

class User extends Authenticatable implements JWTSubject
{
  use Notifiable;
  
  public $table = "users";

	/**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'firstname',
    'lastname',
    'othernames',
    'email',
    'tenant_id',
    'meta',
    'password',
    'tenant',
    'image',
    'user_type_id',
    'account_status_id',
    'access_level_id',
    'user_role_id'
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'password','tenant_id','created_at','updated_at','remember_token','access_level_id','user_type_id','user_role_id','account_status_id'
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
      "user" => $this->only([
        'email',
        'first_name',
        'last_name',
        'id',
      ]),
      "tenant" => $this->tenant()->get()->toArray(),
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
   *  Relationships
   */
  function tenant(){
    return $this->belongsTo('App\Tenant');
  }

  function user_type(){
    return $this->belongsTo('App\UserType','user_type_id');
  }

  function account_status(){
    return $this->belongsTo('App\StatusType','account_status_id');
  }

  function access_level(){
    return $this->belongsTo('App\AccessLevel','access_level_id');
  }
}
