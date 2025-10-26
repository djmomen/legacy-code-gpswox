<?php

namespace App\Http\Controllers\Api\Frontend;

use Tobuli\Entities\User;
use Tobuli\Services\OneTimeTokenService;

class OneTimeTokenController extends BaseController
{
    public function __construct(
        private OneTimeTokenService $ottService
    ) {
        parent::__construct();
    }

    public function __invoke()
    {
        request()->validate([
            'email' => 'required',
        ]);

        $user = User::userAccessible($this->user)->where('email', request('email'))->first();

        if ($user === null) {
            return response('', 422);
        }

        $ott = $this->ottService->create($user);

        if ($ott === null) {
            return response('', 422);
        }

        return response()->json([
            'token' => $ott,
        ]);
    }
}