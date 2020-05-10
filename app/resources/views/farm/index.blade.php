@extends('layout')

@section('pageTitle')
	{{ $htexts['page_farms']->title }} 
@endsection

@section('title')
	{{ $htexts['page_farms']->title }} 
@endsection

@section('content')
	<div class="container">
		<div class="row">
		@foreach( $farms as $farm )
			<div class="col-sm-6">
				<div class="well well-lg">
					<table><tr style='vertical-align:top;'><td>
						<a href='/farm/{{$farm->id}}'>
						@if( empty($farm->icon) )
							<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" style='height:125px; border-radius:12px;'/>
						@else
							<img src="data:image/jpg;base64, {{$farm->icon}}" style='height:125px; border-radius:12px;'/>
						@endif					
						</a>
					</td><td style='padding-left:12px;'>
						<a href='/farm/{{$farm->id}}'>
							<div style='font-size:120%;'>{{ $farm->title }}</div>
						</a>
						<div style='font-size:16px;'>{{ $farm->address }}</div>
					</td></tr></table>
				</div>
			</div>
			@if( $loop->iteration % 2 == 0 )
            	</div>
            	<div class="row">
        	@endif
		@endforeach
		</div>
	</div>
@endsection
       
