<?php

namespace Tobuli\Helpers\BroadcastMessage\UsersFilter;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Entities\User;
use Tobuli\Services\EntityLoader\UsersLoader;

class UsersFilter implements FilterInterface, UserAwareInterface
{
    private User $user;

    public function apply(Builder $query, array $params): void
    {
        if (empty($params['users'])) {
            return;
        }

        $loader = new UsersLoader($this->user);
        $loader->setRequestKey('receivers.users');
        $loader->setInput(['selected_receivers' => $params]);

        if ($loader->hasAttach()) {
            $query->whereIn('id', $loader->getQueryAttach());
        }
    }

    public function getView(): string
    {
        return 'Admin.BroadcastMessages.Partials.users';
    }

    public function getViewParameters(): array
    {
        return [];
    }

    public function relevant(): bool
    {
        return true;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}