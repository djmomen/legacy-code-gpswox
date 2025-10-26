<?php

namespace Tobuli\Helpers\BroadcastMessage\UsersFilter;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Entities\User;

class ExpiringFilter implements FilterInterface
{
    public function apply(Builder $query, array $params): void
    {
        if (!empty($params['expiring_days'])) {
            $query->isExpiringAfter((int)$params['expiring_days']);
        }
    }

    public function getView(): string
    {
        return 'Admin.BroadcastMessages.Partials.expiring';
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