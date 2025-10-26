<?php

namespace App\Transformers\TraccarPosition;

use App\Transformers\BaseTransformer;
use App\Transformers\SensorsValues\SensorsValuesTraccarPositionTransformer;
use Illuminate\Support\Facades\Cache;
use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarPosition;
use Tobuli\Helpers\Formatter\Facades\Formatter;
use Tobuli\Services\DeviceAnonymizerService;

class TraccarPositionFullTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        'sensors_values',
    ];

    public function transform(?TraccarPosition $entity): ?array
    {
        if ($entity === null) {
            return null;
        }

        $anonymizer = $this->getAnonymizer($entity->device);

        return [
            'id'        => $entity->id,
            'latitude'  => $anonymizer?->isAnonymous($entity) ? null : $entity->latitude,
            'longitude' => $anonymizer?->isAnonymous($entity) ? null : $entity->longitude,
            'time'      => Formatter::time()->human($entity->time),
            'speed'     => Formatter::speed()->format($entity->device->getSpeed($entity)),
            'altitude'  => Formatter::altitude()->format($entity->altitude),
            'other'     => empty($entity->other) ? [] : parseXML($entity->other),
        ];
    }

    public function includeSensorsValues(TraccarPosition $entity)
    {
        return $this->item($entity, new SensorsValuesTraccarPositionTransformer(), false);
    }

    private function getAnonymizer(Device $device)
    {
        return Cache::store('array')->sear("device.$device->id.anonymizer", fn () => new DeviceAnonymizerService($device));
    }
}