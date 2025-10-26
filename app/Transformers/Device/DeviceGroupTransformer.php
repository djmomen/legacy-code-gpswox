<?php

namespace App\Transformers\Device;

use Tobuli\Entities\Device;
use Formatter;

class DeviceGroupTransformer extends DeviceTransformer {

    public function transform(Device $entity)
    {
        return [
            'id' => $entity->group->first()->id ?? 0,
            'title' => $entity->group->first()->title ?? trans('front.ungrouped'),
        ];
    }
}