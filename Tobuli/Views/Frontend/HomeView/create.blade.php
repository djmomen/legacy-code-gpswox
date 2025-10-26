@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon point"></i> {!! trans('front.home_view') !!}
@stop

@section('body')
    {!! Form::open(['route' => 'home_view.store', 'method' => 'POST']) !!}

    {!! Form::hidden('zoom', $zoom) !!}
    {!! Form::hidden('lat', $lat) !!}
    {!! Form::hidden('lon', $lon) !!}

    <div class="form-group">
        {!!Form::label('name', trans('front.home_view_confirm'))!!}
    </div>

    {!! Form::close() !!}

    <script>
        $(document).ready(function() {
            const zoom = app.map.getZoom();
            const center = app.map.getCenter();
            const lat = center.lat;
            const lon = center.lng;

            let form = $('#home_view');

            form.find('input[name="zoom"]').val(zoom);
            form.find('input[name="lat"]').val(lat);
            form.find('input[name="lon"]').val(lon);
        });
    </script>
@stop