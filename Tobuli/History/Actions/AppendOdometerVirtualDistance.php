<?php

namespace Tobuli\History\Actions;


use Illuminate\Database\QueryException;
use Tobuli\Entities\Device;

class AppendOdometerVirtualDistance extends ActionAppend
{
    protected $sensors = [];

    static public function required(){
        return [
            AppendDistanceGPS::class
        ];
    }

    public function boot()
    {
        $sensors = $this->getDevice()->getSensorsByType('odometer');

        if ( ! $sensors)
            return;

        $distance = null;

        foreach ($sensors as & $sensor)
        {
            if ($sensor->shown_value_by != 'virtual_odometer')
                continue;

            if (is_null($distance)) {
                $distance = self::getValue($this->getDevice(), $this->getDateFrom());
            }

            $value = floatval($sensor->value);

            $sensor->value = $value ? ($value - $distance) : $value;

            $this->sensors[] = $sensor;
        }
    }

    public function proccess(&$position)
    {
        foreach ($this->sensors as $sensor) {
            $sensor->value += $position->distance_gps;
        }
    }

    public static function getValue(Device $device, $dateFrom)
    {
        try {
            return $device
                ->positions()
                ->where('time', '>=', $dateFrom)
                ->where('valid', '>', 0)
                ->sum('distance');

        } catch (QueryException $e) {
            if ($e->getCode() !== '42S02') {
                throw $e;
            }

            return 0;
        }
    }
}