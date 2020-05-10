@extends('layout')

@section('pageTitle')
	{{ $farm->title }}	
@endsection

@section('title')
	{{ $farm->title }}
@endsection

@section('head_extra')
    <link rel="stylesheet" href="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/css/ol.css" type="text/css">
@endsection

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-sm-4">
				@if( empty($farm->icon) )
					<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" style='height:100px; border-radius:12px;'/>
				@else
					<img src="data:image/jpg;base64, {{$farm->icon}}" style='height:200px; border-radius:12px;'/>
				@endif				
			</div>
			<div class = "col-sm-8">	
				<div class="well">
					<h3>{{ $farm->title }}</h3>
					<div>{{ $farm->address }}</div>
				</div>
				<div style='font-size:16px; font-style:italic;'>{{ $farm->descr }}</div>
				@include( '../map.map', [ 'params' => "'farm={$farm->id}'" ] )
			</div>
		</div>
	</div>
@endsection
       

