<?php

namespace Tobuli\Helpers\BroadcastMessage\JobBuilder;

use App\Jobs\Broadcaster\AbstractBroadcastJob;
use App\Jobs\Broadcaster\SmsBroadcastJob;
use Illuminate\Database\Eloquent\Builder;
use Tobuli\Entities\BroadcastMessage;
use Tobuli\Entities\User;

class SmsJobBuilder implements JobBuilderInterface
{
    public function buildUsersQuery(): Builder
    {
        return User::query()->whereNotNull('phone_number');
    }

    public function buildBroadcastJob(BroadcastMessage $message): AbstractBroadcastJob
    {
        return new SmsBroadcastJob($message);
    }
}