@extends('layout')

@section('pageTitle') 
	{{ $htexts['page_publications']->title }} 
@endsection

@section('title')
	{{ $htexts['page_publications']->title }} 
@endsection

@section('content')
	<div class="container">
		@foreach( $publications as $publication )

		<div class="row">
			<div class="col-sm-3">
				<a href='/publication/{{$publication->id}}'>
					@if( empty($publication->icon) )
						<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" style='max-height:200px; border-radius:12px;'/>
					@else
						<img src="data:image/jpg;base64, {{$publication->icon}}" style='max-height:200px; border-radius:12px;'/>
					@endif				
				</a>
			</div>
			<div class = "col-sm-9">	
				<div class="well well-lg">
					<div style='color:#7f7f7f; font-size:15px;'>[ {{ $publication->created_at }} ]</div>
					<h3>{{ $publication->title }}</h3>
					<a href='/publication/{{$publication->id}}'>
						<div style='font-size:16px; font-style:italic;'>{{ $publication->descr }}</div>
					</a>
				</div>
			</div>
		</div>

		@endforeach
	</div>
@endsection
       
