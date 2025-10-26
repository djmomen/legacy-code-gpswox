<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class BroadcastMessageProgress extends Progress
{
    public \Tobuli\Entities\BroadcastMessage $broadcastMessage;

    public function __construct($broadcastMessage)
    {
        $this->broadcastMessage = $broadcastMessage;
    }

    public function broadcastOn()
    {
        if (!$this->broadcastMessage->user) {
            return [];
        }

        return [
            $this->broadcastMessage->user->getSocketChannel()
        ];
    }

    protected static function getType(): string
    {
        return 'broadcast_message';
    }

    protected function getID(): int
    {
        return $this->broadcastMessage->id;
    }

    protected function getTotal(): int
    {
        return (int)$this->broadcastMessage->total;
    }

    protected function getCompleted(): int
    {
        return (int)$this->broadcastMessage->success;
    }
}