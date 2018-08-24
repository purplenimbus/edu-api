<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
   /**
     * Cast meta property to array
     *
     * @var array
     */
	 
	protected $casts = [
        'meta' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description','meta','name'
    ];
}
