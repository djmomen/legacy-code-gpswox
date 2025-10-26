<?php

namespace App\Transformers\SensorsValues;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceSensor;
use Tobuli\Entities\TraccarPosition;
use Tobuli\History\Actions\AppendOdometerVirtualDistance;

class SensorsValuesTraccarPositionTransformer extends BaseTransformer
{
    public function transform(TraccarPosition $entity): array
    {
        $device = $entity->device;

        $result = [];

        foreach ($device->sensors as $sensor) {
            $result[] = [
                'id'            => $sensor->id,
                'type'          => $sensor->type,
                'name'          => $sensor->formatName(),
                'show_in_popup' => $sensor->show_in_popup,
                'value'         => $this->getValuePosition($device, $sensor, $entity),
                'tag_name'      => $sensor->tag_name,
            ];
        }

        return $result;
    }

    private function getValuePosition(Device $device, DeviceSensor $sensor, TraccarPosition $position): mixed
    {
        if ($sensor->type === 'odometer' && $sensor->shown_value_by === 'virtual_odometer') {
            $distance = AppendOdometerVirtualDistance::getValue($device, $position->time);

            $value = floatval($sensor->value);

            return $value ? ($value - $distance) : $value;
        }

        return $sensor->getValuePosition($position);
    }
}