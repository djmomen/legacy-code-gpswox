<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tobuli\Entities\Device;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('devices_imei_logs')) {
            return;
        }

        Schema::create('devices_imei_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('device_id');
            $table->unsignedBigInteger('traccar_device_id');
            $table->string('imei');
            $table->timestamp('created_at');
        });

        DB::table('devices_imei_logs')->insertUsing(
            ['device_id', 'traccar_device_id', 'imei', 'created_at'],
            Device::whereNotNull('traccar_device_id')
                ->select('id', 'traccar_device_id', 'imei', 'updated_at')
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices_imei_logs');
    }
};
