<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid as Uuid;

class Billing extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'details','status_id'
    ];

    public function registrations(){
        return $this->hasMany('App\Registrations');
    }

    public function status(){
        return $this->belongsTo('App\BillingStatus','status_id');
    }

    /**
     * Cast meta property to array
     *
     * @var array
     */
	protected $casts = [
        'meta' => 'array',
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
