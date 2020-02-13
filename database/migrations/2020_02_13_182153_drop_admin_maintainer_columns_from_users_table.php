<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\User;

class DropAdminMaintainerColumnsFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Seed the roles we will need and migrate all the admins and maintainers
         * to their respective new roles.
         */

        $seed = new RolesTableSeeder();
        $seed->run();

        $admins = User::where('admin', true)->get();
        $maintainers = User::where('maintainer', true)->get();

        /** @var User $user */
        foreach ($admins as $user) {
            $user->grantRole('Administrator');
        }

        foreach ($maintainers as $user) {
            $user->grantRole('Editor');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin');
            $table->dropColumn('maintainer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('admin')->default(false);
            $table->boolean('maintainer')->default(false);
        });

        /*
         * Migrate all the admins and editors to their old table schema.
         */
        $admins = User::administrators()->get();
        $editors = User::editors()->get();

        /** @var User $user */
        foreach ($admins as $user) {
            $user->revokeRole('Administrator');

            $user->update(['admin' => true]);
        }

        foreach ($editors as $user) {
            $user->revokeRole('Editor');

            $user->update(['maintainer' => true]);
        }
    }
}
