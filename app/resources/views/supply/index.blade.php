@extends('layout')

@section('pageTitle')
	{{ $htexts['page_supplies']->title }} 
@endsection

@section('title')
	{{ $htexts['page_supplies']->title }} 
@endsection

@section('content')
	<div class="container">
		@foreach( $supplies as $supply )
		<div class="row">
			<div class="col-sm-3">
				<a href='/supply/{{$supply->id}}'>
					@if( empty($supply->icon) )
						<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" 
							style='height:150px; border-radius:12px;'/>
					@else
						<img src="data:image/jpg;base64, {{$supply->icon}}" 
							style='height:150px; border-radius:12px;'/>
					@endif				
				</a>
			</div>
			<div class = "col-sm-9">	
				<div class="well well-lg">
					<a href='/supply/{{$supply->id}}'>
						<h3>{{ $supply->title }}</h3>
					</a>					
						<div style='color:#7f7f7f; font-size:15px;'>{{ $supply->deliver_to }}</div>
						<div style='color:#7f7f7f; font-size:14px;'>{!! $supply->descr !!}</div>
				</div>
			</div>
		</div>
		@endforeach

		@if( count($supplies_delivered) > 0 )
			<div style='margin:28px 0px 44px 0px; border-top:8px solid #f0f0f0;'></div> 
			@foreach( $supplies_delivered as $supply )
			<div class="row">
				<div class="col-sm-3">
					<a href='/supply/{{$supply->id}}'>
					@if( empty($supply->icon) )
						<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" 
							style='height:150px; border-radius:12px;'/>
					@else
						<img src="data:image/jpg;base64, {{$supply->icon}}" 
							style='height:150px; border-radius:12px;'/>
					@endif				
					</a>
				</div>
				<div class = "col-sm-9">	
					<div class="well well-lg">
						<a href='/supply/{{$supply->id}}'>
							<h3>{{ $supply->title }}</h3>
						</a>					
						<div style='color:#7f7f7f; font-size:15px;'>{{ $supply->deliver_to }}</div>
					</div>
				</div>
			</div>
			@endforeach
		@endif
	</div>
@endsection
       
