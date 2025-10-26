<?php

namespace App\Transformers\Device;

use App\Transformers\BaseTransformer;
use App\Transformers\DeviceImage\DeviceImageTransformer;
use App\Transformers\Driver\DriverFullTransformer;
use App\Transformers\Tag\TagListTransformer;
use League\Fractal\Resource\Item;
use Tobuli\Entities\Device;

abstract class DeviceTransformer extends BaseTransformer
{
    protected array $includesLoadMap = [
        'position' => ['sensors', 'traccar'],
        'group' => ['group'],
    ];

    protected $availableIncludes = [
        'position',
        'icon',
        'sensors',
        'services',
        'driver',
        'users',
        'image',
        'tags',
        'group'
    ];

    public function includePosition(Device $device) {
        return $this->item($device, new DevicePositionTransformer(), false);
    }

    public function includeIcon(Device $device) {
        return $this->item($device, new DeviceIconTransformer(), false);
    }

    public function includeSensors(Device $device) {
        return $this->item($device, new DeviceSensorsTransformer(), false);
    }

    public function includeServices(Device $device) {
        return $this->item($device, new DeviceServicesTransformer(), false);
    }

    public function includeDriver(Device $device) {
        if ( ! $device->driver)
            return null;

        return $this->item($device->driver, new DriverFullTransformer(), false);
    }

    public function includeUsers(Device $device) {
        return $this->item($device, new DeviceUsersTransformer(), false);
    }

    public function includeImage(Device $entity): Item
    {
        return $this->item($entity, new DeviceImageTransformer(), false);
    }

    public function includeTags(Device $entity)
    {
        if (!$this->user->can('view', $entity, 'tags')) {
            return null;
        }

        return $this->collection($entity->tags, new TagListTransformer(), false);
    }

    public function includeGroup(Device $entity)
    {
        return $this->item($entity, new DeviceGroupTransformer(), false);
    }
}