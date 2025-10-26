<?php

namespace Tobuli\Helpers\SMS\Services;

interface SmsSenderInterface
{
    public function send($receiver_phone, $message_body);

    public function isEnabled(): bool;
}