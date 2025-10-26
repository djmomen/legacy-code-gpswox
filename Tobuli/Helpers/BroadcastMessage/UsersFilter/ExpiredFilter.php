<?php

namespace Tobuli\Helpers\BroadcastMessage\UsersFilter;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Entities\User;

class ExpiredFilter implements FilterInterface
{
    public function apply(Builder $query, array $params): void
    {
        if (!empty($params['expired'])) {
            $query->isExpiredBefore(0);
        }
    }

    public function getView(): string
    {
        return 'Admin.BroadcastMessages.Partials.expired';
    }

    public function getViewParameters(): array
    {
        return [];
    }

    public function relevant(): bool
    {
        return User::hasExpirationDate()->count() ? true : false;
    }
}