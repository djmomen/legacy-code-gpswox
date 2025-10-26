<?php

namespace Tobuli\Helpers\BroadcastMessage\JobBuilder;

use App\Jobs\Broadcaster\AbstractBroadcastJob;
use Illuminate\Database\Eloquent\Builder;
use Tobuli\Entities\BroadcastMessage;

interface JobBuilderInterface
{
    public function buildUsersQuery(): Builder;

    public function buildBroadcastJob(BroadcastMessage $message): AbstractBroadcastJob;
}