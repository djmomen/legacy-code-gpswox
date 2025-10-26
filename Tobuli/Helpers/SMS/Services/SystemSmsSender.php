<?php

namespace Tobuli\Helpers\SMS\Services;

class SystemSmsSender extends AbstractProxySmsSender
{
    public function __construct()
    {
        parent::__construct(settings('sms_gateway'));
    }
}