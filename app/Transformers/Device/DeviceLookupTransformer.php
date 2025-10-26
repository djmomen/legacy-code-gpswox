<?php

namespace App\Transformers\Device;

use Formatter;
use Tobuli\Entities\Device;

class DeviceLookupTransformer extends DeviceTransformer
{
    protected static function requireLoads()
    {
        return ['traccar'];
    }

    public function transform(Device $entity)
    {
        $expirationDate = $this->canView($entity, 'expiration_date');
        $expirationDate = $expirationDate ? Formatter::time()->convert($expirationDate) : null;

        $data = [
            'id'                  => (int)$entity->id,
            'active'              => (boolean)$entity->active,
            'kind'                => $entity->kind,
            'name'                => $entity->name,
            'imei'                => $this->canView($entity, 'imei'),
            'device_type_id'      => $this->canView($entity, 'device_type_id'),
            'fuel_type'           => $entity->fuel_type,
            'fuel_emissions'      => $entity->fuel_emissions,
            'sim_number'          => $this->canView($entity, 'sim_number'),
            'device_model'        => $this->canView($entity, 'device_model'),
            'plate_number'        => $this->canView($entity, 'plate_number'),
            'vin'                 => $this->canView($entity, 'vin'),
            'registration_number' => $this->canView($entity, 'registration_number'),
            'object_owner'        => $this->canView($entity, 'object_owner'),
            'additional_notes'    => $this->canView($entity, 'additional_notes'),
            'protocol'            => $this->canView($entity, 'protocol'),
            'expiration_date'     => $expirationDate,
        ];

        if (config('addon.device_custom_data')) {
            $data['custom_data'] = $entity->custom_data;
        }

        return $data;
    }
}