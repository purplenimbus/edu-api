<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Webpatser\Uuid\Uuid as Uuid;

class Tenant extends Model
{
   // use Notifiable;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','meta','username','email'
    ];
	
	/**
     * Cast meta property to array
     *
     * @var object
     */
	 
	protected $casts = [
        'meta' => 'object',
    ];
	
	/**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at'
    ];
	
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
}
