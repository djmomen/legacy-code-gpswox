<?php

namespace App\Transformers\ClientLite;

use Formatter;
use Tobuli\Entities\Event;

class EventTransformer extends BaseTransformer {

    /**
     * @param Event $entity
     * @return array|null
     */
    public function transform($entity)
    {
        return [
            'id'     => intval($entity->id),
            'alert'  => $entity->alert ? [
                'id' => intval($entity->alert->id),
                'name' => $entity->alert->name,
            ] : null,
            'device' => $entity->device ? [
                'id' => intval($entity->device->id),
                'name' => $entity->device->name,
            ] : null,
            'name'   => $entity->name,
            'detail' => $entity->detail,
            'time'   => $this->serializeDateTime($entity->time),
            'speed'  => $this->serializeFormatter(Formatter::speed(), $entity->speed),
            'icon'   => $entity->getIconAsset(),
            'coordinates' => [
                'lat'    => $entity->latitude,
                'lng'    => $entity->longitude,
            ],
        ];
    }

    protected static function requireLoads()
    {
        return ['device', 'geofence', 'alert'];
    }
}