<?php

namespace Tobuli\Helpers\SMS;

use Illuminate\Support\Facades\Cache;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\SMS\Services\AppSmsSender;
use Tobuli\Helpers\SMS\Services\HttpGetSmsSender;
use Tobuli\Helpers\SMS\Services\PlivoSmsSender;
use Tobuli\Helpers\SMS\Services\HttpPostSmsSender;
use Tobuli\Helpers\SMS\Services\ServerSmsSender;
use Tobuli\Helpers\SMS\Services\SmsSenderInterface;
use Tobuli\Helpers\SMS\Services\SystemSmsSender;

class SMSGatewayManager
{
    public const MAP_SENDERS = [
        'get'       => HttpGetSmsSender::class,
        'post'      => HttpPostSmsSender::class,
        'plivo'     => PlivoSmsSender::class,
        'app'       => AppSmsSender::class,
        'server'    => ServerSmsSender::class,
        'system'    => SystemSmsSender::class,
    ];

    public function loadSender(User $user, ?array $gateway_args = null): SmsSenderInterface
    {
        if (is_null($gateway_args))
            $gateway_args = $this->getGatewayArguments($user);

        if (empty($gateway_args['request_method']))
            throw new ValidationException(['sender_service' => trans('validation.sms_gateway_error')]);

        $sender = self::getSender($gateway_args['request_method'], $gateway_args);

        if (!$sender) {
            throw new ValidationException(['sender_service' => trans('validation.sms_gateway_error')]);
        }

        return $sender;
    }

    public static function getSender(string $method, array $settings): ?SmsSenderInterface
    {
        if (!isset(self::MAP_SENDERS[$method])) {
            return null;
        }

        $sender = self::MAP_SENDERS[$method];
        $sender = new $sender($settings);

        if (!$sender->isEnabled()) {
            return null;
        }

        return $sender;
    }

    /**
     * @param $user
     * @param $test_args
     * @return mixed
     */
    private function getGatewayArguments($user)
    {
        return $this->getUserGatewayArgs($user);
    }

    /**
     * @param $user
     * @return mixed
     */
    protected function getUserGatewayArgs($user)
    {
        $gateway_args = $user->sms_gateway_params;
        $gateway_args['sms_gateway_status'] = $user->sms_gateway;
        $gateway_args['sms_gateway_url'] = $user->sms_gateway_url;
        $gateway_args['user_id'] = $user->id;

        return $gateway_args;
    }

    /**
     * @param $user_id
     * @return null|User
     */
    private function getUser($user_id)
    {
        if (is_null($user_id))
            return null;

        return Cache::store('array')->rememberForever("user.$user_id", function() use ($user_id) {
            return User::find($user_id);
        });
    }

    public static function getRequestMethods(): array
    {
        $requestMethods = [
            'get' => 'GET',
            'post' => 'POST',
            'app' => trans('front.sms_gateway_app'),
            'plivo' => 'Plivo'
        ];

        if (settings('sms_gateway.enabled')) {
            $requestMethods = ['server' => 'Server gateway'] + $requestMethods;
        }

        return $requestMethods;
    }

    public static function getRequestMethodTypes(): array
    {
        return array_keys(self::getRequestMethods());
    }
}