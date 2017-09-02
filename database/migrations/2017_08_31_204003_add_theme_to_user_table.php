<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThemeToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add theme column to users table
        Schema::table('users', function ($table) {

            $table->string('theme')->default('bootswatch/slate');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // remove theme column from users table
        Schema::table('users', function ($table) {

            $table->dropColumn('theme');
        });
    }
}
