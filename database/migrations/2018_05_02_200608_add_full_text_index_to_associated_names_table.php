<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFullTextIndexToAssociatedNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE FULLTEXT INDEX assoc_name_index ON associated_names(name);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('associated_names', function (BLueprint $table) {
            $table->dropIndex('assoc_name_index');
        });
    }
}
