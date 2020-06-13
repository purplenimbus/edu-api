<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurriculumCourseLoadsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('curriculum_course_loads', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('curriculum_id');
      $table->integer('subject_id');
      $table->integer('type_id');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('curriculum_course_loads');
  }
}
