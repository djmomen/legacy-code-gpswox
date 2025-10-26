<?php

namespace Tobuli\Services;

use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\SMS\SMSGatewayManager;

class SystemSmsService
{
    private $smsSender = null;

    /**
     * @throws ValidationException
     */
    public function __construct()
    {
        try {
            $this->smsSender = (new SMSGatewayManager())
                ->loadSender(new User(), ['request_method' => 'system']);
        } catch (\InvalidArgumentException $e) {}
    }

    public function send(string $phone, string $message)
    {
        $this->smsSender->send($phone, $message);
    }

    public function isEnabled(): bool
    {
        $settings = settings('sms_gateway');

        return $settings['enabled'] && $settings['use_as_system_gateway'];
    }
}
