<?php

namespace Tobuli\Observers;

use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Device;

class DeviceImeiObserver
{
    public function created(Device $device): void
    {
        $this->logChange($device);
    }

    public function updated(Device $device): void
    {
        if ($device->wasChanged('imei')) {
            $this->logChange($device);
        }
    }

    public function deleted(Device $device): void
    {
        $this->logChange($device);
    }

    private function logChange(Device $device): void
    {
        DB::table('devices_imei_logs')->insert([
            'device_id'         => $device->id,
            'traccar_device_id' => $device->traccar_device_id,
            'imei'              => $device->imei,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);
    }
}
