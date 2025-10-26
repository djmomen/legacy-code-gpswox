@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon icon-fa fa-bullhorn"></i> {{ trans('admin.broadcast_message') }}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#message-form-main" role="tab" data-toggle="tab">{{ trans('front.main') }}</a></li>
        <li>
            <a href="#message-form-receivers" role="tab" data-toggle="tab">
                {{ trans('admin.receivers') }}

                <!-- data-table -->
                <span data-table>
                    @include('Admin.BroadcastMessages.users_count')
                </span>
            </a>
        </li>
    </ul>

    {!! Form::open(['route' => 'admin.broadcast_messages.store', 'method' => 'POST', 'id' => 'broadcast_message']) !!}

    <div class="tab-content">
        <div id="message-form-main" class="tab-pane active">
            <div class="form-group">
                {!! Form::label('channels[]', trans('admin.channel'), ['class' => 'control-label']) !!}
                {!! Form::select('channels[]', $channels, null, ['class' => 'form-control', 'multiple' => 'multiple']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('title', trans('validation.attributes.title'), ['class' => 'control-label']) !!}
                {!! Form::text('title', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('content', trans('admin.content'), ['class' => 'control-label']) !!}
                {!! Form::textarea('content', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div id="message-form-receivers" class="tab-pane">
            @foreach ($userFilters as $userFilter)
                @if ($userFilter->relevant())
                <div class="form-group">
                    @include($userFilter->getView())
                </div>
                @endif
            @endforeach
        </div>
    </div>
    {!! Form::close() !!}

    <script>
        tables.set_config('broadcast_messages_create', {
            url: '{{ route("admin.broadcast_messages.users_count") }}'
        });
    </script>
@stop

