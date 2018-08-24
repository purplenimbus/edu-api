<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurriculumType extends Model
{
    public $table = "curricula_types";

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
        'description','meta','country'
    ];
}
