@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon event-add"></i> {{ trans('front.duplicates') }}
@stop

@section('body')
    {!!Form::open(['route' => ['admin.broadcast_messages.update', $item->id], 'method' => 'PUT'])!!}

    <div class="panel-body">
        <div class="form-group">
            {!! Form::label('title', trans('validation.attributes.title'), ['class' => 'col-xs-12 col-sm-4 control-label']) !!}
            <div class="col-xs-12">
                {!! Form::text('title', $channels[$item->channel], ['class' => 'form-control', 'disabled' => 'disabled']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('title', trans('validation.attributes.title'), ['class' => 'col-xs-12 col-sm-4 control-label']) !!}
            <div class="col-xs-12">
                {!! Form::text('title', $item->title, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('content', trans('admin.content'), ['class' => 'col-xs-12 col-sm-4 control-label']) !!}
            <div class="col-xs-12">
                {!! Form::textarea('content', $item->content, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@stop
