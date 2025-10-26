<?php

namespace App\Jobs\Broadcaster;

use App\Jobs\Job;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\BroadcastMessage;
use Tobuli\Entities\User;
use Tobuli\Helpers\BroadcastMessage\BroadcastManager;

abstract class AbstractBroadcastJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public function __construct(
        protected BroadcastMessage $message,
    ) {}

    public function handle(): void
    {
        $broadcastManger = new BroadcastManager($this->message->user);

        $usersQuery = $broadcastManger
            ->resolveJobBuilder($this->message->channel)
            ->buildUsersQuery();

        foreach ($broadcastManger->getUserFilters() as $filter) {
            $filter->apply($usersQuery, $this->message->filters);
        }

        $this->message->update(['status' => BroadcastMessage::STATUS_IN_PROGRESS]);

        $usersQuery->chunk(500, function ($users) {
            foreach ($users as $user) {
                try {
                    $this->process($user);
                } catch (\Exception) {
                    $this->message->fail++;
                }

                $this->message->success++;

                if ($this->message->updated_at->diffInSeconds(now()) > 1) {
                    $this->message->save();
                }
            }
        });

        $this->message->update([
            'status' => $this->message->fail ? BroadcastMessage::STATUS_FAILED : BroadcastMessage::STATUS_COMPLETED,
        ]);
    }

    abstract protected function process(User $user): void;
}