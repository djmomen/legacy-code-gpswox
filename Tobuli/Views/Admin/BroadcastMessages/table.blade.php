<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeaderSort($items->sorting, 'channel', trans('admin.channel')) !!}
            {!! tableHeaderSort($items->sorting, 'title') !!}
            {!! tableHeaderSort($items->sorting, 'content', trans('admin.content')) !!}
            {!! tableHeader('front.progress') !!}
        </tr>
        </thead>
        <tbody>
        @php /** @var \Tobuli\Entities\BroadcastMessage $item */@endphp
        @forelse ($items->getCollection() as $item)
            <tr>
                <td>
                    <div class="checkbox">
                        <input type="checkbox" value="{!! $item->id !!}">
                        <label></label>
                    </div>
                </td>
                <td>{{ $item->channel }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->content }}</td>

                <td id="{{ \App\Events\BroadcastMessageProgress::key($item->id) }}">
                    @if ($item->status === \Tobuli\Entities\BroadcastMessage::STATUS_IN_PROGRESS)
                        @php
                            $done = $item->success;
                            $total = $item->total;
                            $percentage = $total ? (int)($done / $total * 100) : 100;
                        @endphp

                        <div class="progress">
                            <div class="progress-bar"
                                 role="progressbar"
                                 aria-valuenow="{{ $done }}"
                                 aria-valuemin="0"
                                 aria-valuemax="{{ $total }}"
                                 style="width: {{ $percentage }}%;">
                                {{ $percentage }}%
                            </div>
                        </div>
                    @else
                        {{ $item->status_title }}
                    @endif
                </td>
            </tr>
        @empty
            <tr class="">
                <td class="no-data" colspan="9">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@include("Admin.Layouts.partials.pagination")
