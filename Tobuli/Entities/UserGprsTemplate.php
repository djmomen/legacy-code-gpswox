<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Builder;

class UserGprsTemplate extends CommandTemplate
{
    const TYPE = 'gprs';

    public function scopeCommonAccessible(Builder $query, User $user): Builder
    {
        if (!$user->perm('global_gprs_templates', 'view'))
            return $query;

        return parent::scopeCommonAccessible($query, $user);
    }
}
