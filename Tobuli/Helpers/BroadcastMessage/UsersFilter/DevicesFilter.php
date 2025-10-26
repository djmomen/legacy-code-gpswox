<?php

namespace Tobuli\Helpers\BroadcastMessage\UsersFilter;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Entities\User;
use Tobuli\Services\EntityLoader\DevicesLoader;

class DevicesFilter implements FilterInterface, UserAwareInterface
{
    private User $user;

    public function apply(Builder $query, array $params): void
    {
        if (empty($params['devices'])) {
            return;
        }

        $loader = new DevicesLoader($this->user);
        $loader->setRequestKey('receivers.devices');
        $loader->setInput(['selected_receivers' => $params]);

        if ($loader->hasAttach()) {
            $query->whereHas('devices', fn ($query) => $query
                ->whereIn('devices.id', $loader->getQueryAttach())
            );
        }
    }

    public function getView(): string
    {
        return 'Admin.BroadcastMessages.Partials.devices';
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