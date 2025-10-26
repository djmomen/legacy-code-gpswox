<?php

namespace App\Transformers\DeviceService;

use App\Transformers\Device\DeviceListTransformer;
use Tobuli\Entities\DeviceService;

class DeviceServiceFullTransformer extends DeviceServiceTransformer
{
    protected $availableIncludes = ['device'];

    public function transform(?DeviceService $entity): ?array
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'                     => (int) $entity->id,
            'user_id'                => (int) $entity->user_id,
            'device_id'              => (int) $entity->device_id,
            'name'                   => (string) $entity->name,
            'expiration_by'          => $entity->expiration_by,
            'interval'               => (int) $entity->interval,
            'last_service'           => $entity->last_service,
            'trigger_event_left'     => (int) $entity->trigger_event_left,
            'renew_after_expiration' => (bool) $entity->renew_after_expiration,
            'expires'                => $entity->expiration(),
            'expires_date'           => $entity->expires_date,
            'remind'                 => (double) $entity->remind,
            'remind_date'            => $entity->remind_date,
            'event_sent'             => (bool) $entity->event_sent,
            'expired'                => (bool) $entity->expired,
            'email'                  => $entity->email,
            'mobile_phone'           => $entity->mobile_phone,
            'description'            => $entity->description,
        ];
    }

    public function includeDevice(DeviceService $entity)
    {
        return $this->item($entity->device, new DeviceListTransformer(), false);
    }
}
