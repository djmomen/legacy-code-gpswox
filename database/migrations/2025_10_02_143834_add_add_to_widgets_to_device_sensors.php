<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tobuli\Entities\DeviceSensor;
use Tobuli\Entities\SensorGroupSensor;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('sensor_group_sensors', 'add_to_widgets')) {
            return;
        }

        Schema::table('device_sensors', function (Blueprint $table) {
            $table->boolean('add_to_widgets')->after('add_to_graph')->default(false);
        });

        Schema::table('sensor_group_sensors', function (Blueprint $table) {
            $table->boolean('add_to_widgets')->after('add_to_graph')->default(false);
        });

        DeviceSensor::query()->update(['add_to_widgets' => true]);
        SensorGroupSensor::query()->update(['add_to_widgets' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('sensor_group_sensors', 'add_to_widgets')) {
            return;
        }

        Schema::table('device_sensors', function (Blueprint $table) {
            $table->dropColumn('add_to_widgets');
        });

        Schema::table('sensor_group_sensors', function (Blueprint $table) {
            $table->dropColumn('add_to_widgets');
        });
    }
};
