<?php

namespace App\Jobs\Broadcaster;

use App\Events\BroadcastMessage;
use Tobuli\Entities\User;

class SocketBroadcastJob extends AbstractBroadcastJob
{
    protected function process(User $user): void
    {
        event(new BroadcastMessage($user, $this->message->content));
    }
}
