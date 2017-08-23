<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssociatedNameReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('associated_name_references', function (Blueprint $table) {
            $table->unsignedInteger('manga_id')->references('id')->on('manga');
            $table->unsignedInteger('assoc_name_id')->references('id')->on('associated_names');

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
        //
        Schema::dropIfExists('associated_name_references');
    }
}
