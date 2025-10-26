<div class="form-group">
    <div class="checkbox">
        {!! Form::hidden('receivers[expired]', 0) !!}
        {!! Form::checkbox('receivers[expired]', 1, null, ['data-filter' => 'true']) !!}
        {!! Form::label('receivers[expired]', trans('front.expired')) !!}
    </div>
</div>
