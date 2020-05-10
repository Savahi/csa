@extends('layout')

@section('pageTitle')
	{{ $dp->title }}	
@endsection

@section('title')
	{{ $dp->title }}
@endsection

@section('head_extra')
    <link rel="stylesheet" href="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/css/ol.css" type="text/css">
@endsection

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-sm-3">
				@if( empty($dp->icon) )
					<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" style='max-height:200px; border-radius:12px;'/>
				@else
					<img src="data:image/jpg;base64, {{$dp->icon}}" style='max-height:200px; border-radius:12px;'/>
				@endif				
			</div>
			<div class = "col-sm-9">	
				<div class="well well-lg">
					<h3>{{ $dp->title }}</h3>
					<div style='font-style:italic;'>{{ $dp->descr }}</div>
					<div style='font-style:italic;'>{{ $dp->address }}</div>
					<div style='font-style:normal;'><b>Информация для участников</b>: {{ $dp->pickup_info}}</div>
				</div>
				@include( '../map.map', [ 'params' => "'delivery_point={$dp->id}'" ] )
			</div>
		</div>
	</div>
@endsection
