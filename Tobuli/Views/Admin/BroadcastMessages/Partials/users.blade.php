{!! Form::label('receivers[users][]', trans('admin.users'), ['class' => 'control-label"']) !!}
{!! Form::select('receivers[users][]', [], null, [
    'class' => 'form-control',
    'multiple' => 'multiple',
    'data-live-search' => 'true',
    'data-filter' => 'true',
    'data-actions-box' => 'true',
    'data-ajax' => route('admin.clients.users')
]) !!}

