<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountIdColumnToEarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->unsignedInteger('account_id')->default(0);
            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign('account_id_foreign');
            $table->dropColumn('account_id');
        });
    }
}
