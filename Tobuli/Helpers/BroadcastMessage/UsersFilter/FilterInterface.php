<?php

namespace Tobuli\Helpers\BroadcastMessage\UsersFilter;

use Illuminate\Database\Eloquent\Builder;

interface FilterInterface
{
    public function apply(Builder $query, array $params): void;

    public function getView(): string;

    public function getViewParameters(): array;

    public function relevant(): bool;
}