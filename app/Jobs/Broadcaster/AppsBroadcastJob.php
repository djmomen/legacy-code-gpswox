<?php

namespace App\Jobs\Broadcaster;

use Tobuli\Entities\User;
use Tobuli\Services\FcmService;

class AppsBroadcastJob extends AbstractBroadcastJob
{
    private FcmService $fcmService;

    protected function process(User $user): void
    {
        $this->getFcmService()->send($user, $this->message->title, $this->message->content);
    }

    private function getFcmService(): FcmService
    {
        return $this->fcmService
            ?? $this->fcmService = new FcmService();
    }
}
