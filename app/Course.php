<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid as Uuid;

class Course extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description', 'meta','tenant_id','instructor_id','subject_id','code','course_grade_id'
    ];
	
    /**
     * Get course registrations
     *
     * @var array
     */
    public function grade()
    {
        return $this->belongsTo('App\CourseGrade','course_grade_id');
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
     * Get course registrations
     *
     * @var array
     */
	public function registrations()
    {
        return $this->hasMany('App\Registration');
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
}
