<?php

namespace Tobuli\Helpers\SMS\Services;

use Tobuli\Helpers\SMS\SMSGatewayManager;

abstract class AbstractProxySmsSender implements SmsSenderInterface
{
    protected SmsSenderInterface $sender;

    public function __construct(
        protected array $settings,
    ) {
        $this->setSender($settings);
    }

    protected function setSender(array $settings): void
    {
        if (!isset($settings['request_method'])) {
            throw new \InvalidArgumentException('`request_method` not provided');
        }

        $sender = SMSGatewayManager::getSender($settings['request_method'], $settings);

        if (!$sender) {
            throw new \InvalidArgumentException('Unsupported request method');
        }

        if ($sender instanceof AbstractProxySmsSender) {
            throw new \InvalidArgumentException('Self-referencing settings');
        }

        $this->sender = $sender;
    }

    public function send($receiver_phone, $message_body)
    {
        return $this->sender->send($receiver_phone, $message_body);
    }

    public function isEnabled(): bool
    {
        return true;
    }
}