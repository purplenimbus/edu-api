<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolTerm extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','meta','description','meta'
    ];
	
	/**
     * Cast meta property to array
     *
     * @var object
     */
	 
	protected $casts = [
        'meta' => 'object',
    ];
}
