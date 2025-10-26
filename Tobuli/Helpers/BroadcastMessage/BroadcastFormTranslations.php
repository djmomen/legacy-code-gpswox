<?php

namespace Tobuli\Helpers\BroadcastMessage;

use App\Http\Requests\BroadcastMessageRequest;
use Tobuli\Services\SystemSmsService;

class BroadcastFormTranslations
{
    public static function getUserGroups(): array
    {
        return [
            '1' => trans('admin.group_1'),
            '3' => trans('admin.group_3'),
            '2' => trans('admin.group_2'),
            '4' => trans('admin.group_4'),
            '5' => trans('admin.group_5'),
            '6' => trans('admin.group_6'),
        ];
    }

    public static function getChannels(): array
    {
        $systemSmsService = resolve(SystemSmsService::class);

        $channels = [
            BroadcastMessageRequest::TYPE_EMAIL => trans('validation.attributes.email'),
            BroadcastMessageRequest::TYPE_APPS => trans('front.mobile_apps'),
            BroadcastMessageRequest::TYPE_SOCKET => trans('validation.attributes.popup_notification'),
        ];

        if ($systemSmsService->isEnabled()) {
            $channels[BroadcastMessageRequest::TYPE_SMS] = trans('front.sms');
        }

        return $channels;
    }
}