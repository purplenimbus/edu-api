<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentProfileGradesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('payment_profiles', function (Blueprint $table) {
      $table->integer('student_grade_id')->nullable();
      $table->integer('school_term_type_id')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('payment_profiles', function (Blueprint $table) {
      $table->dropColumn('student_grade_id');
      $table->dropColumn('school_term_type_id');
    });
  }
}
