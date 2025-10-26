{!! Form::label('receivers[devices][]', trans('front.devices'), ['class' => 'control-label"']) !!}
{!! Form::select('receivers[devices][]', [], null, [
    'class' => 'form-control',
    'multiple' => 'multiple',
    'data-live-search' => 'true',
    'data-filter' => 'true',
    'data-actions-box' => 'true',
    'data-ajax' => route('devices.index'),
]) !!}

