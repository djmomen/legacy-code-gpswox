<?php

namespace Tobuli\Helpers\SMS\Services;

use Tobuli\Entities\User;

class ServerSmsSender extends AbstractProxySmsSender
{
    public function __construct()
    {
        parent::__construct(settings('sms_gateway'));
    }

    public function isEnabled(): bool
    {
        if (empty($this->settings['enabled'])) {
            return false;
        }

        if ($this->settings['request_method'] === 'app'
            && !runCacheEntity(User::class, $this->settings['user_id'])->first()
        ) {
            return false;
        }

        return true;
    }
}