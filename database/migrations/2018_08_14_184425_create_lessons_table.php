<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('lessons', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('tenant_id');
      $table->integer('course_id');
      $table->integer('parent_id')->nullable();
      $table->string('title');
      $table->mediumText('description')->nullable();
      $table->longText('content')->nullable();
      $table->json('meta')->nullable();
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
    Schema::dropIfExists('lessons');
  }
}
