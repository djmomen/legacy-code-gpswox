<?php namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphMapServiceProvider extends ServiceProvider {

    public function boot()
    {
        Relation::morphMap([
            'device'              => \Tobuli\Entities\Device::class,
            'user'                => \Tobuli\Entities\User::class,
            'task'                => \Tobuli\Entities\Task::class,
            'billing_plan'        => \Tobuli\Entities\BillingPlan::class,
            'device_plan'         => \Tobuli\Entities\DevicePlan::class,
            'device_service'      => \Tobuli\Entities\DeviceService::class,
            'device_sensor'       => \Tobuli\Entities\DeviceSensor::class,
            'alert'               => \Tobuli\Entities\Alert::class,
            'report'              => \Tobuli\Entities\Report::class,
            'event'               => \Tobuli\Entities\Event::class,
            'geofence'            => \Tobuli\Entities\Geofence::class,
            'route'               => \Tobuli\Entities\Route::class,
            'poi'                 => \Tobuli\Entities\Poi::class,
            'sensor_group_sensor' => \Tobuli\Entities\SensorGroupSensor::class,
        ]);
    }

    public function register() {}
}
