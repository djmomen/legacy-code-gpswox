{!! Form::label('receivers[user_groups][]', trans('validation.attributes.groups'), ['class' => 'control-label"']) !!}
{!! Form::select('receivers[user_groups][]', $userGroups, null, ['multiple' => 'multiple', 'class' => 'form-control', 'data-filter' => 'true']) !!}

