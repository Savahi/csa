@extends('layout')

@section('pageTitle')
	{{ $publication->title }}	
@endsection

@section('title')
	{{ $publication->title }}
@endsection

@section('content')
	<!--<div style='text-align:center;'><a href='/publication/'>[ Публикации ]</a></div>-->
	<div class="container">
		<div class="row">
			<div class="col-sm-3">
				@if( empty($publication->icon) )
					<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" style='max-height:200px; border-radius:12px;'/>
				@else
					<img src="data:image/jpg;base64, {{$publication->icon}}" style='max-height:200px; border-radius:12px;'/>
				@endif				
			</div>
			<div class = "col-sm-9">	
				<div class="well well-lg">
					<div style='color:#7f7f7f; font-size:15px;'>[ {{ $publication->created_at}} ]</div>
					<h3>{{ $publication->title }}</h3>
					<div style='font-size:16px; font-style:italic;'>{{ $publication->descr }}</div>
				</div>
				<div>{{ $publication->text }}</div>
			</div>
		</div>
	</div>
@endsection
       
