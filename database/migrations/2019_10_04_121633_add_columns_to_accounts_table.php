<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('real_name', 200);
            $table->float('balance');
            $table->float('available_balance');
            $table->dateTime('open_date');
            $table->string('branch_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('real_name');
            $table->dropColumn('balance');
            $table->dropColumn('available_balance');
            $table->dropColumn('open_date');
            $table->dropColumn('branch_name');
        });
    }
}

