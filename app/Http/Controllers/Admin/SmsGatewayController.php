<?php namespace App\Http\Controllers\Admin;


use CustomFacades\Validators\SMSGatewayFormValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\SMS\SMSGatewayManager;


class SmsGatewayController extends BaseController
{

    public function index()
    {
        $data = [
            'params' => settings('sms_gateway'),
            'users'  => User::where('group_id', 1)->get()->pluck('email', 'id')->all()
        ];

        return View::make('admin::Sms_gateway.index')->with($data);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'request_method' => ['required', Rule::in(SMSGatewayManager::getRequestMethodTypes())],
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator->errors());
            }

            SMSGatewayFormValidator::validate($request['request_method'], $request->all([
                'sms_gateway_url', 'username', 'password', 'custom_headers', 'auth_id', 'auth_token', 'senders_phone'
            ]));
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->getErrors());
        }

        settings('sms_gateway', $request->except('_token'));

        return redirect()->back()->withSuccess(trans('front.successfully_saved'));
    }
}
