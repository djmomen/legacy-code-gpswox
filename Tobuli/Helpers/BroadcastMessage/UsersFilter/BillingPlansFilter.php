<?php

namespace Tobuli\Helpers\BroadcastMessage\UsersFilter;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Entities\BillingPlan;

class BillingPlansFilter implements FilterInterface
{
    public function apply(Builder $query, array $params): void
    {
        if (!empty($params['billing_plans'])) {
            $query->whereIn('billing_plan_id', $params['billing_plans']);
        }
    }

    public function getView(): string
    {
        return 'Admin.BroadcastMessages.Partials.billing_plans';
    }

    public function getViewParameters(): array
    {
        return ['billingPlans' => BillingPlan::all()->pluck('title', 'id')];
    }

    public function relevant(): bool
    {
        return BillingPlan::count() ? true : false;
    }
}
