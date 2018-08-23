<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->integer('tenant_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('othernames')->nullable();
            $table->string('title')->nullable();
            $table->string('password')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('image')->nullable();
            $table->integer('access_level')->default(1);
            $table->json('meta')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
