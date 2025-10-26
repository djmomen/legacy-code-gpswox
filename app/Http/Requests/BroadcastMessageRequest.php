<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class BroadcastMessageRequest extends Request
{
    const TYPE_SMS = 'sms';
    const TYPE_EMAIL = 'email';
    const TYPE_APPS = 'apps';
    const TYPE_SOCKET = 'socket';

    private array $types = [
        self::TYPE_SMS,
        self::TYPE_EMAIL,
        self::TYPE_APPS,
        self::TYPE_SOCKET
    ];

    private array $titleAwareTypes = [self::TYPE_EMAIL, self::TYPE_APPS];

    private array $typesContentMaxLength = [
        self::TYPE_SMS => 160,
        self::TYPE_EMAIL => 65000,
        self::TYPE_APPS => 100,
        self::TYPE_SOCKET => 200,
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'channels' => 'required|array|' . Rule::in($this->types),
            'receivers' => 'required|array',
            'content' => 'required|min:3',
        ];

        $types = $this->input('channels', []);

        if ($max = $this->findMaxContentLength($types)) {
            $rules['content'] .= '|max:' . $max;
        }

        if ($this->isTitleRequired($types)) {
            $rules['title'] = 'required';
        }

        return $rules;
    }

    private function findMaxContentLength(array $channels): int
    {
        $max = 0;

        foreach ($channels as $channel) {
            if (!isset($this->typesContentMaxLength[$channel])) {
                continue;
            }

            $typeMax = $this->typesContentMaxLength[$channel];

            if ($max === 0 || $typeMax < $max) {
                $max = $typeMax;
            }
        }

        return $max;
    }

    private function isTitleRequired(array $channels): bool
    {
        foreach ($channels as $channel) {
            if (in_array($channel, $this->titleAwareTypes)) {
                return true;
            }
        }

        return false;
    }
}
