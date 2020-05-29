<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_groups', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('owner_id');
      $table->integer('tenant_id');
      $table->integer('type_id');
      $table->mediumText('description')->nullable();
      $table->string('name')->nullable();
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
    Schema::dropIfExists('user_groups');
  }
}
