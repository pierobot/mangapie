<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMuNameDistanceToMangaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manga', function (Blueprint $table) {
            $table->text('mu_name')->nullable();
            $table->float('distance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manga', function (Blueprint $table) {
            $table->dropColumn('mu_name');
            $table->dropColumn('distance');
        });
    }
}
