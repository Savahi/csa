@extends('layout')

@section('pageTitle') 
	{{ $htexts['page_crops']->title }} 
@endsection

@section('title')
	{{ $htexts['page_crops']->title }} 
@endsection

@section('content')
<div class="container">
	<div class="row">
		@foreach( $crops as $crop )
			<!--include( 'plugin.record_list_item', ['title'=>'TITLE', 'icon'=>'{{$crop->icon}}', 'desc'=>'DESC', 'href'=>'/' ] )-->
			<div class="col-sm-6">
				<div class="well well-lg">
					<table><tr style='vertical-align:top;'><td>
						<a href='/crop/{{$crop->id}}'>
						@if( empty($crop->icon) )
							<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" style='height:100px; border-radius:12px;'/>
						@else
							<img src="data:image/jpg;base64, {{$crop->icon}}" style='height:100px; border-radius:12px;'/>
						@endif					
						</a>
					</td><td style='padding-left:12px;'>
						<a href='/crop/{{$crop->id}}'><b>{{ $crop->title }}</b></a>
						<div style='font-style:italic;'>{{ $crop->descr }}</div>
						<div style=''><span class="glyphicon glyphicon-grain"><b>{{ $crop->count }}</b></span></div>
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
       
