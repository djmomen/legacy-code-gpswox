<?php

namespace Tobuli\Helpers\BroadcastMessage\UsersFilter;

use Tobuli\Entities\User;

interface UserAwareInterface
{
    public function setUser(User $user): void;
}