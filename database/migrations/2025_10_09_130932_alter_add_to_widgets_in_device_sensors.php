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
        Schema::table('device_sensors', function (Blueprint $table) {
            $table->boolean('add_to_widgets')->nullable()->change();
        });

        Schema::table('sensor_group_sensors', function (Blueprint $table) {
            $table->boolean('add_to_widgets')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_sensors', function (Blueprint $table) {
            $table->boolean('add_to_widgets')->nullable(false)->default(false)->change();
        });

        Schema::table('sensor_group_sensors', function (Blueprint $table) {
            $table->boolean('add_to_widgets')->nullable(false)->default(false)->change();
        });
    }
};
