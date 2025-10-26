<?php

namespace App\Jobs\Broadcaster;

use Tobuli\Entities\User;
use Tobuli\Services\SystemSmsService;

class SmsBroadcastJob extends AbstractBroadcastJob
{
    protected function process(User $user): void
    {
        $smsService = resolve(SystemSmsService::class);

        $smsService->send($user->phone_number, $this->message->content);
    }
}
