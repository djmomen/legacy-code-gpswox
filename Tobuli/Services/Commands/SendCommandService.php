<?php namespace Tobuli\Services\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tobuli\Entities\SentCommand;
use Tobuli\Entities\UserSmsTemplate;
use Tobuli\Helpers\Tracker;
use Tobuli\Protocols\Commands;
use Tobuli\Protocols\Manager as ProtocolsManager;

class SendCommandService
{
    const CONNECTION_GPRS = 'gprs';
    const CONNECTION_SMS = 'sms';

    private $tracker;
    private $protocolManager;
    private $actor;
    private $user;

    public function __construct($actor = null)
    {
        $this->protocolManager = new ProtocolsManager();
        $this->tracker = new Tracker();
        $this->actor = $actor;
    }

    public function send($devices, $data, $user, $connection)
    {
        switch ($connection) {
            case null:
            case self::CONNECTION_GPRS:
                return $this->gprs($devices, $data, $user);

            case self::CONNECTION_SMS:
                return $this->sms($devices, $data, $user);

            default:
                throw new \RuntimeException("Unknown commands type '$connection'");
        }
    }

    public function gprs($devices, $data, $user)
    {
        return $this->sendCommand(self::CONNECTION_GPRS, compact('devices', 'data', 'user'));
    }

    public function sms($devices, $data, $user)
    {
        $data['type'] = $data['type'] ?? 'custom';

        if (Str::startsWith($data['type'], 'template_'))
            list($data['type'], $data['template_id']) = explode('_', $data['type']);

        $message = null;

        if ($data['type'] == 'template') {
            $template = UserSmsTemplate::userAccessible($user)->find($data['template_id']);
            $message = $template->message ?? '';
        }

        $message = $user->perm('send_command', 'edit') && !empty($data['message'])
            ? $data['message']
            : $message;

        $data = [
            'message'     => $message,
            'type'        => $data['type'],
            'template_id' => $data['template_id'] ?? null,
        ];

        return $this->sendCommand(self::CONNECTION_SMS, compact('devices', 'data', 'user'));
    }

    public function setActor($actor)
    {
        $this->actor = $actor;
    }

    public function sendStored(SentCommand $cmd, int $maxAttempts = 0): array
    {
        $this->actor = $cmd->user;
        $this->user = $cmd->user;

        $data = $cmd->attributesToArray();
        $data['type'] = $cmd->command;

        if ( ! $this->user)
            return ['status' => 0, 'error' => trans('front.user_not_found')];

        if ( ! $this->user->can('show', $cmd->device))
            return ['status' => 0, 'error' => trans('front.dont_have_permission')];

        if (in_array($data['type'], ['custom', 'serial']) && ! $this->user->perm('send_command', 'edit'))
            return ['status' => 0, 'error' => trans('front.dont_have_permission')];

        if ($maxAttempts && $cmd->attempts >= $maxAttempts) {
            $results = [
                'status' => SentCommand::STATUS_FAIL,
                'message' => 'Attempts limit reached',
            ];
        } else {
            $command = $cmd->parameters;
            $command['uniqueId'] = $cmd->device->imei;

            $results = $this->tracker->sendCommand($command);
            $results['status']= 1;
        }

        if ($results['status'] == 0)
            $results['error'] = $results['message'];

        $cmd->attempts++;

        if ($results['status'] == SentCommand::STATUS_SUCCESS) {
            $cmd->status = $results['status'];
            $cmd->response = null;
        } else {
            $cmd->status = $maxAttempts && $cmd->attempts >= $maxAttempts
                ? SentCommand::STATUS_FAIL
                : SentCommand::STATUS_PENDING;
            $cmd->response = $results['error'];
        }

        $cmd->save();

        return $results;
    }

    private function sendCommand($connection, $arguments)
    {
        $arguments['data']['connection'] = $connection;
        $this->actor = $this->actor ?: $arguments['user'];
        $this->user = $arguments['user'];

        if ( ! ($arguments['devices'] instanceof Collection || is_array($arguments['devices'])))
            $arguments['devices'] = [$arguments['devices']];

        $responses = new Collection();

        foreach ($arguments['devices'] as $device) {
            $response = $this->{"_$connection"}($device, $arguments['data']);

            $response['device'] = $device->name;

            $responses->push($response);
        }

        return $responses;
    }

    private function _sms($device, $data)
    {
        if ( ! $this->user)
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.user_not_found')]);

        if ( ! $this->user->perm('sms_gateway', 'view'))
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.dont_have_permission')]);

        if ( ! $this->user->can('show', $device))
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.dont_have_permission')]);

        $message = $this->prepareSmsMessage($device, $data['message']);

        if (empty($message))
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.empty')]);

        $result = sendSMS($device->sim_number, $message, $this->user);

        $this->logSending($device, $data, [
            'parameters' => ['message' => $message],
            'status'     => $result['status'],
        ]);

        return $result;
    }

    private function _gprs($device, $data)
    {
        if (Str::startsWith($data['type'], 'template_'))
            list($data['type'], $data['template_id']) = explode('_', $data['type']);

        if ( ! $this->user)
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.user_not_found')]);

        if ( ! $this->user->can('show', $device))
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.dont_have_permission')]);

        if (in_array($data['type'], ['custom', 'serial']) && ! $this->user->perm('send_command', 'edit'))
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.dont_have_permission')]);

        if ($device->gprs_templates_only && ! Str::startsWith($data['type'], 'template'))
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.no_templates')]);

        if ($error = $this->checkSpeedLimit($device, $data))
            return $this->handleError($device, $data, ['status' => 0, 'error' => $error]);

        if ($device->protocol == 'demo') {
            return [
                'status' => 1
            ];
        }

        if ( ! $device->isConnected()) {
            if (!empty($data['auto_send_when_online'])) {
                $command = $this->protocolManager->protocol($device->protocol)->buildCommand($device, $data);

                unset($command['uniqueId']);

                $this->logSending($device, $data, [
                    'status'     => SentCommand::STATUS_PENDING,
                    'parameters' => $command,
                    'attempts'   => 0,
                ]);

                return ['status' => 0, 'error' => trans('front.added_to_the_waiting_queue')];
            }

            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.no_gprs_connection')]);
        }

        $command = $this->protocolManager
            ->protocol($device->protocol)
            ->buildCommand($device, $data instanceof SentCommand ? $data->parameters : $data);

        $results = $this->tracker->sendCommand($command);

        $this->logSending($device, $data, [
            'status'     => $results['status'],
            'parameters' => Arr::get($command, 'attributes'),
            'response'   => Arr::get($results, 'message'),
        ]);

        if ($results['status'] == 0)
            $results['error'] = $results['message'];

        return $results;
    }

    private function handleError($device, $data, $results)
    {
        $this->logSending($device, $data, [
            'parameters' => null,
            'response'   => $results['error'],
            'status'     => $results['status'],
        ]);

        return $results;
    }

    private function logSending($device, $data, $additional)
    {
        $this->actor->sentCommands()->create([
                'user_id'     => $this->user->id,
                'device_imei' => $device->imei,
                'template_id' => empty($data['template_id']) ? null : $data['template_id'],
                'connection'  => $data['connection'],
                'command'     => $data['type'],
                'attempts'    => $data['attempts'] ?? 1,
            ] + $additional);
    }

    private function prepareSmsMessage($device, $message)
    {
        return strtr($message, [
            '[%IMEI%]' => $device->imei
        ]);
    }

    private function checkSpeedLimit($device, $data)
    {
        $plugin = settings('plugins.send_command_speed_limit');

        if (!Arr::get($plugin, 'status')) {
            return null;
        }

        if ($device->getSpeed() < Arr::get($plugin, 'options.speed_limit')) {
            return null;
        }

        $messages = explode(';', Arr::get($plugin, 'options.messages'));
        $commands = Arr::get($plugin, 'options.commands');

        $containCommand = in_array($data['type'], $commands);
        $containMessage = !empty($data[Commands::KEY_DATA]) && in_array($data[Commands::KEY_DATA], $messages);

        if (!($containCommand || $containMessage)) {
            return null;
        }

        return trans('front.send_command_speed_limit_fail');
    }

}