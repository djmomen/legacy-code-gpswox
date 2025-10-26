<?php

namespace App\Events;

use Tobuli\Entities\User;

class BroadcastMessage extends NoticeEvent
{
    public function __construct(User $actor, string $content)
    {
        parent::__construct($actor, NoticeEvent::TYPE_INFO, $content);
    }
}
