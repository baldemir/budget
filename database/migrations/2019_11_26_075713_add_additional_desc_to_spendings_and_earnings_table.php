<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalDescToSpendingsAndEarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spendings', function (Blueprint $table) {
            // add new column if not exist
            $table->string('additional_desc' , 1000);
        });

        Schema::table('earnings', function (Blueprint $table) {
            // add new column if not exist
            $table->string('additional_desc' , 1000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spendings', function (Blueprint $table) {
            // remove column if exist
            $table->dropColumn('additional_desc');
        });

        Schema::table('earnings', function (Blueprint $table) {
            // remove column if exist
            $table->dropColumn('additional_desc');
        });
    }
}
