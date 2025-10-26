<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Transformers\Device\DeviceLookupTransformer;
use Illuminate\Http\Request;

class DevicesController extends BaseController
{
    public function index(Request $request)
    {
        $this->checkException('devices', 'view');

        $query = $this->user
            ->devices()
            ->filter($request->all())
            ->search($request->get('s'))
            ->includes($request->get('includes'));

        $devices = $query->paginate($request->get('limit', 50));

        $devices->appends($request->except('user_api_hash'));

        return response()->json(array_merge(
            ['status' => 1],
            \FractalTransformer::paginate($devices, DeviceLookupTransformer::class)->toArray()
        ));
    }
}
