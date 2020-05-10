@extends('layout')

@section('pageTitle')
	{{ $crop->title }}	
@endsection

@section('title')
	{{ $crop->title }}
@endsection

@section('content')
	<div class="container">
		<div class="well well-lg">

				<table><tr style='vertical-align:top;'><td>
					@if( empty($crop->icon) )
						<img src="data:image/png;base64, {{Config::get('myconstants.emptyIcon')}}" style='height:100px; border-radius:12px;'/>
					@else
						<img src="data:image/jpg;base64, {{$crop->icon}}" style='height:100px; border-radius:12px;'/>
					@endif					
				</td><td style='padding-left:12px;'>
					<h3>{{ $crop->title }}</h3>
					<div style='font-style:italic;'>{{ $crop->descr }}</div>
				</td></tr></table>

		</div>
	</div>
@endsection
       
