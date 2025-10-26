<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Builder;

class UserSmsTemplate extends CommandTemplate
{
    const TYPE = 'sms';

    public function scopeCommonAccessible(Builder $query, User $user): Builder
    {
        if (!$user->perm('global_sms_templates', 'view'))
            return $query;

        return parent::scopeCommonAccessible($query, $user);
    }
}
