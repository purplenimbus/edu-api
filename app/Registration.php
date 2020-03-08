<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid as Uuid;
use App\CourseScore;

class Registration extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'course_id','user_id','meta','tenant_id','term_id','billing_id'
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [];

	/**
   * Cast meta property to array
   *
   * @var array
   */

	protected $casts = [];

  public function course(){
    return $this->belongsTo('App\Course');
  }

  public function user(){
    return $this->belongsTo('App\User');
  }

  public function term(){
    return $this->belongsTo('App\SchoolTerm','term_id');
  }

  public function course_score(){
    return $this->hasOne('App\CourseScore','id','course_score_id');
  }
	/**
	 *  Setup model event hooks
	 */
	public static function boot()
	{
		parent::boot();
		self::created(function ($model) {
      if ($model->course->schema) {
        $course_score = CourseScore::create([
          'registration_id' => $model->id,
          'scores' => array_map(function($item) {
            $item['score'] = 0;
            return $item;
          },
            $model->course->schema,
          ),
        ]);
        $model->course_score_id = $course_score->id;
        $model->save();
      }
		});
	}
}
