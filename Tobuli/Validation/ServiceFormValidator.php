<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;
use Illuminate\Validation\Rule;
use Tobuli\Helpers\HereMaps;
use Tobuli\Helpers\LbsLocation\LbsManager;

class ServiceFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name' => 'required',
            'interval' => 'required|numeric|min:1',
            'expiration_by' => 'required|in:days,odometer,engine_hours',
            'trigger_event_left' => 'required|numeric|min:1|lesser_than:interval',
            'description' => 'string|max:255',
            'renew_after_expiration' => 'boolean',
            'allow_expired_value' => 'boolean',
        ],
        'update' => [
            'name' => 'required',
            'interval' => 'required|numeric|min:1',
            'expiration_by' => 'required|in:days,odometer,engine_hours',
            'trigger_event_left' => 'required|numeric|min:1|lesser_than:interval',
            'description' => 'string|max:255',
            'renew_after_expiration' => 'boolean',
            'allow_expired_value' => 'boolean',
        ],
    ];

    public function __construct(IlluminateValidator $validator)
    {
        $this->rules['create']['last_service'] = [
            'required',
            Rule::when(request()->get('expiration_by') == 'days', ['date']),
            Rule::when(request()->get('expiration_by') == 'odometer', ['numeric']),
            Rule::when(request()->get('expiration_by') == 'engine_hours', ['numeric'])
        ];

        $this->rules['update']['last_service'] = [
            'required',
            Rule::when(request()->get('expiration_by') == 'days', ['date']),
            Rule::when(request()->get('expiration_by') == 'odometer', ['numeric']),
            Rule::when(request()->get('expiration_by') == 'engine_hours', ['numeric'])
        ];

        parent::__construct($validator);
    }
}   //end of class


//EOF