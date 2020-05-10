@extends('layout')

@section('pageTitle')
	{{ $htexts['page_delivery_points']->title }} 
@endsection

@section('title')
	{{ $htexts['page_delivery_points']->title }} 
@endsection

@section('content')
	<div class="container">
		@foreach( $dps as $dp )

		<div class="row">
			<div class="col-sm-3">
				<a href='/delivery_point/{{$dp->id}}'>
					@if( empty($dp->icon) )
						<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" 
							style='height:150px; border-radius:12px;'/>
					@else
						<img src="data:image/jpg;base64, {{$dp->icon}}" 
							style='height:150px; border-radius:12px;'/>
					@endif				
				</a>
			</div>
			<div class = "col-sm-9">	
				<div class="well well-lg">
					<a href='/delivery_point/{{$dp->id}}'>
						<h3>{{ $dp->title }}</h3>
					</a>					
					<div style='font-style:italic;'>{{ $dp->descr }}</div>
					<div style='font-style:italic;'>{{ $dp->address }}</div>
					<div style='font-style:normal;'><b>Информация для участников</b>: {{ $dp->pickup_info}}</div>
				</div>
			</div>
		</div>

		@endforeach
	</div>
@endsection
       
