<?php

namespace Tobuli\Helpers\BroadcastMessage\JobBuilder;

use App\Jobs\Broadcaster\AbstractBroadcastJob;
use App\Jobs\Broadcaster\EmailBroadcastJob;
use Illuminate\Database\Eloquent\Builder;
use Tobuli\Entities\BroadcastMessage;
use Tobuli\Entities\User;

class EmailJobBuilder implements JobBuilderInterface
{
    public function buildUsersQuery(): Builder
    {
        return User::query();
    }

    public function buildBroadcastJob(BroadcastMessage $message): AbstractBroadcastJob
    {
        return new EmailBroadcastJob($message);
    }
}