<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('device_expenses', 'invoice_number')) return;

        Schema::table('device_expenses', function (Blueprint $table) {
            $table->string('invoice_number')->after('additional')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ( ! Schema::hasColumn('device_expenses', 'invoice_number')) return;

        Schema::table('device_expenses', function ($table) {
            $table->dropColumn('invoice_number');
        });
    }
};
