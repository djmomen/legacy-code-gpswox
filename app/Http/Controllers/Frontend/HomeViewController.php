<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Tobuli\Helpers\UserHomeViewService;

class HomeViewController extends Controller
{
    public function __construct(
        private UserHomeViewService $homeViewService
    ) {
        parent::__construct();
    }

    protected function afterAuth($user)
    {
        $this->homeViewService->setUser($user);
    }

    public function index()
    {
        $this->checkException('home_view', 'view');

        return $this->homeViewService->get();
    }

    public function create()
    {
        $this->checkException('home_view', 'store');

        return view('Frontend.HomeView.create')->with([
            'lat' => request('lat'),
            'lon' => request('lon'),
            'zoom' => request('zoom'),
        ]);
    }

    public function store()
    {
        $this->checkException('home_view', 'store');

        try {
            $this->homeViewService->set(request()->all());
        } catch (ValidationException) {
            return response()->json(['status' => 0, 'message' => trans('front.unexpected_error')]);
        }

        return response()->json(['status' => 1, trans('front.home_view') . ' - ' . trans('front.successfully_saved')]);
    }
}
