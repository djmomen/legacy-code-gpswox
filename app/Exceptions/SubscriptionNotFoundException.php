<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;


class SubscriptionNotFoundException extends HttpException
{
    public function __construct(?string $id = null)
    {
        parent::__construct(404, trans('global.subscription_not_found', ['id' => $id]));
    }
}