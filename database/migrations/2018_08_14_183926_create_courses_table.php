<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('courses', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('tenant_id');
      $table->integer('instructor_id')->nullable();
      $table->integer('student_grade_id')->nullable();
      $table->integer('subject_id')->nullable();
      $table->string('name')->nullable();
      $table->string('code')->nullable();
      $table->string('description')->nullable();
      $table->json('meta')->nullable();
      $table->json('schema')->nullable();
      $table->integer('status_id');
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
    Schema::dropIfExists('courses');
  }
}
