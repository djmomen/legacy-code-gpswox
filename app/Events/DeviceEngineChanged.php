<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\Device;

class DeviceEngineChanged extends Event
{
    use SerializesModels;

    public function __construct(
        public Device $device,
        public ?string $time = null,
    ) {}
}
