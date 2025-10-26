{!! Form::label('receivers[expiring_days]', trans('front.expires_in_days', ['days' => null]), ['class' => 'control-label"']) !!}
{!! Form::number('receivers[expiring_days]', null, ['class' => 'form-control', 'data-filter' => 'true']) !!}

