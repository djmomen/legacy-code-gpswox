<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BroadcastMessageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Tobuli\Entities\BroadcastMessage;
use Tobuli\Entities\User;
use Tobuli\Helpers\BroadcastMessage\BroadcastFormTranslations;
use Tobuli\Helpers\BroadcastMessage\BroadcastManager;
use Tobuli\Helpers\BroadcastMessage\Message;

class BroadcastMessagesController extends BaseController
{
    private BroadcastManager $broadcastManager;

    public function __construct()
    {
        if (!config('addon.broadcast_messages')) {
            abort(404);
        }

        parent::__construct();
    }

    protected function afterAuth($user)
    {
        $this->broadcastManager = new BroadcastManager($user);
    }

    public function index(Request $request)
    {
        $input = $request->all();

        $sort = $input['sorting'] ?? ['sort_by' => 'updated_at', 'sort' => 'desc'];

        $items = BroadcastMessage::userOwned($this->user)
            ->search($input['search_phrase'] ?? null)
            ->toPaginator($input['limit'] ?? 20, $sort['sort_by'], $sort['sort']);

        return $this->api
            ? $items
            : view('Admin.BroadcastMessages.' . ($request->ajax() ? 'table' : 'index'))
                ->with(compact('items'));
    }

    public function getUsersCount()
    {
        $userFilters = $this->broadcastManager->getUserFilters();

        $receiversQuery = User::query();
        $receiversCriteria = array_merge(
            request('receivers', []),
            request('selected_receivers', [])
        );

        foreach ($userFilters as $filter) {
            $filter->apply($receiversQuery, $receiversCriteria);
        }

        $data['receiversCount'] = $receiversQuery->count();

        return view('Admin.BroadcastMessages.users_count')->with($data);
    }

    public function create()
    {
        $userFilters = $this->broadcastManager->getUserFilters();

        $data = [
            'channels' => BroadcastFormTranslations::getChannels(),
            'userFilters' => $userFilters,
            'receiversCount' => User::query()->count(),
        ];

        foreach ($userFilters as $filter) {
            $data += $filter->getViewParameters();
        }

        return view('Admin.BroadcastMessages.create')->with($data);
    }

    public function store(BroadcastMessageRequest $request)
    {
        $receivers = array_merge(
            request('receivers', []),
            request('selected_receivers', [])
        );

        $this->broadcastManager->broadcast(
            $this->user,
            new Message(
                $request->input('channels'),
                $receivers,
                $request->input('title') ?: '',
                $request->input('content')
            )
        );

        return ['status' => 1];
    }

    public function edit(int $id)
    {
        $item = BroadcastMessage::findOrFail($id);

        $data = [
            'item' => $item,
            'channels' => BroadcastFormTranslations::getChannels(),
        ];

        return view('Admin.BroadcastMessages.edit')->with($data);
    }

    public function update(int $id)
    {
        $item = BroadcastMessage::userOwned($this->user)->findOrFail($id);

        $this->broadcastManager->broadcast(
            $this->user,
            new Message(
                [$item->channel],
                $item->filters,
                $item->title,
                $item->content
            )
        );

        return  ['status' => 1];
    }

    public function destroy() {
        $ids = \Illuminate\Support\Facades\Request::input('id');

        if (empty($ids))
            return Response::json(['status' => 0]);

        BroadcastMessage::whereIn('id', $ids)->delete();

        return Response::json(['status' => 1]);
    }
}
