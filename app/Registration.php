<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CourseScore;
use Illuminate\Database\Eloquent\SoftDeletes;
use Bouncer;

class Registration extends Model
{
  use SoftDeletes;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'course_id', 'user_id', 'meta', 'tenant_id', 'term_id'
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

  public function course()
  {
    return $this->belongsTo('App\Course');
  }

  public function user()
  {
    return $this->belongsTo('App\Student', 'user_id', 'id');
  }

  public function term()
  {
    return $this->belongsTo('App\SchoolTerm', 'term_id', 'id');
  }

  public function score()
  {
    return $this->hasOne('App\CourseScore', 'id', 'course_score_id');
  }

  /**
   *  Setup model event hooks
   */
  public static function boot()
  {
    parent::boot();
    // self::created(function ($model) {
    //   if ($model->course->schema) {
    //     $course_score = CourseScore::create([
    //       'registration_id' => $model->id,
    //       'scores' => array_map(
    //         function ($item) {
    //           $item['score'] = 0;
    //           return $item;
    //         },
    //         $model->course->schema
    //       ),
    //     ]);
    //     $model->course_score_id = $course_score->id;
    //     $model->save();
    //   }

    //   $model->user->allow('view', $model);
    //   $model->user->allow('view', $model->course);

    //   if ($model->user->guardian) {
    //     $model->user->guardian->allow('view', $model);
    //   }
    // });

    // self::deleting(function ($model) {
    //   if ($model->course_score) {
    //     $model->course_score->delete();
    //   }

    //   $model->user->disallow('view', $model->course);

    //   if ($model->user->guardian) {
    //     $model->user->guardian->disallow('view', $model);
    //   }
    // });
  }
}
