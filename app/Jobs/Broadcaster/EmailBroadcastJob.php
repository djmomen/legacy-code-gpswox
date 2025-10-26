<?php

namespace App\Jobs\Broadcaster;

use CustomFacades\MailHelper;
use Tobuli\Entities\User;

class EmailBroadcastJob extends AbstractBroadcastJob
{
    protected function process(User $user): void
    {
        MailHelper::send($user->email, $this->message->title, $this->message->content);
    }
}
