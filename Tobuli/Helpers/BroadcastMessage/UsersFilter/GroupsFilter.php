<?php

namespace Tobuli\Helpers\BroadcastMessage\UsersFilter;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Helpers\BroadcastMessage\BroadcastFormTranslations;

class GroupsFilter implements FilterInterface
{
    public function apply(Builder $query, array $params): void
    {
        if (!empty($params['user_groups'])) {
            $query->whereIn('group_id', $params['user_groups']);
        }
    }

    public function getView(): string
    {
        return 'Admin.BroadcastMessages.Partials.user_groups';
    }

    public function getViewParameters(): array
    {
        return ['userGroups' => BroadcastFormTranslations::getUserGroups()];
    }

    public function relevant(): bool
    {
        return true;
    }
}