@extends('layouts.app')
@section('content')
<div>
    {{-- <div class="start-point">
        <h4>start</h4>
        <select name="s_point" id="s_point">
            <option value="">選択してください</option>
            @foreach ($list as $value)
                <option value="{{$value->id}}" data-lat="{{$value->lat}}" data-lng="{{$value->lng}}">{{$value->place_name}}</option>
            @endforeach
        </select>
    </div>
    <div class="waypoints-point">
        <h4>waypoints</h4>
        <select name="w_point" id="w_point">
            <option value="">選択してください</option>
            @foreach ($list as $value)
                <option value="{{$value->id}}" data-lat="{{$value->lat}}" data-lng="{{$value->lng}}">{{$value->place_name}}</option>
            @endforeach
        </select>
    </div>
    <div class="end-point">
        <h4>end</h4>
        <select name="e_point" id="e_point">
            <option value="">選択してください</option>
            @foreach ($list as $value)
                <option value="{{$value->id}}" data-lat="{{$value->lat}}" data-lng="{{$value->lng}}">{{$value->place_name}}</option>
            @endforeach
        </select>
    </div> --}}
    <button id="root_btn" onclick="ajax_route();">Go</button>
</div>
<div id="map" style="height: 750px"></div>
<div id="directionsPanel"></div>


<script type="text/javascript" src="{{ asset('js/map.js')}}"></script>
<!-- Async script executes immediately and must be after any DOM elements used in callback. -->
<script async defer src="http://maps.google.com/maps/api/js?key=AIzaSyBxsT_3AdDYWLBH8c_gfEY6uNkd_7o77iU&callback=initialize"></script>
@endsection
