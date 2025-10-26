<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

abstract class Progress extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    protected abstract function getID(): int;
    protected abstract function getTotal(): int;
    protected abstract function getCompleted(): int;
    protected abstract static function getType(): string;

    public function broadcastAs()
    {
        return 'progress';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->getID(),
            'key' => $this->key($this->getID()),
            'type' => $this->getType(),
            'total' => $this->getTotal(),
            'value' => $this->getCompleted(),
            'proc' => $this->getTotal() ? $this->getCompleted() / $this->getTotal() * 100 : 0,
        ];
    }

    public static function key($id): string
    {
        return 'progress-' . static::getType() . '-' . $id;
    }
}