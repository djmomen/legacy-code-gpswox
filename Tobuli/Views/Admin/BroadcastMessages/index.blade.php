@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_broadcast_messages">

        <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>

        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="broadcast_messages_create" data-url="{{ route("admin.broadcast_messages.create") }}">
                        <i class="icon icon-fa fa-bullhorn" title="{{ trans('admin.add_new') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title">{{ trans('admin.broadcast_messages') }}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.BroadcastMessages.table')
        </div>
    </div>
@stop

@section('javascript')
    <script>
        tables.set_config('table_broadcast_messages', {
            url: '{{ route("admin.broadcast_messages.index") }}',
            delete_url: '{{ route("admin.broadcast_messages.destroy") }}'
        });

        function broadcast_messages_edit_modal_callback() {
            tables.get('table_broadcast_messages');
        }

        function broadcast_messages_create_modal_callback() {
            tables.get('table_broadcast_messages');
        }
    </script>
@stop