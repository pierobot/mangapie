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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('admin');
            $table->rememberToken();
            $table->timestamps();
        });

        $dev = \App\User::find(1);
        if ($dev == null) {
            $dev = new \App\User;
            $dev->name = 'dev';
            $dev->email = 'fake@email.com';
            $dev->password = '$2y$10$5q/qypVAXnS.qHF7A.C0ke9R5NM0.UHae3WbWIg60BSeBnynFi0m6';
            $dev->admin = true;
            $dev->save();
        }
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
