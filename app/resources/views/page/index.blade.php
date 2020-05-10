@extends('layout')

@section('title')
	{{ $htexts['page_main']->title }}
@endsection

@section('pageTitle')
	{{ $htexts['page_main']->title }}
@endsection

@section('content')

	@if( sizeof($slides) > 0 )
	<div id="myCarousel" class="carousel slide" data-ride="carousel">
	    <!-- Wrapper for slides -->
    	<div class="carousel-inner" role="listbox">
		@foreach( $slides as $slide )
			@if( $loop->iteration == 1 )
			<div class="item active">
			@else
			<div class="item">
			@endif
			<img src="{!!$slide->image_url!!}" alt="{{$slide->title}}" style='width:100%; max-width:1200px; max-height:700px;'>
    	    <div class="carousel-caption" style='padding:4px;'>
				<h2 style='text-shadow:4px 4px 8px #4f4f4f;'>{{$slide->title}}</h2>
				<p style='font-size:125%; text-shadow:4px 4px 16px #000000; background-color:rgba(0,0,0,0.5);'>
					{{$slide->descr}}
				</p>
			</div>      
			</div>
		@endforeach

		    <!-- Left and right controls -->
    		<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
	    	  <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
	    	  <span class="sr-only">Previous</span>
		    </a>
    		<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
		      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    		  <span class="sr-only">Next</span>
	    	</a>
		</div>
	</div>
	@endif

@if ( !Auth::check() )

	<div class='btn-join-container'>
		<div>
			<a href='/participation/'>{!!$htexts['page_participation']->title!!}</a>
		</div>
	</div>

	@if( strlen( $htexts['intro']->title ) > 0 && strlen( $htexts['intro']->descr ) > 0 )
		<div class="container" style='margin-top:0px; padding-top:0px;'>
			<h3>{!! $htexts['intro']->title !!}</h3>
			<p style='font-size:110%;'>
				{!! $htexts['intro']->descr !!}
			</p>
		</div>
	@endif
@endif

<div class="container" id="plugin_main"></div>

<script>
fetch('/plugin_main', {method: 'get'})
	.then( response => response.text() )
	.then( function(response) { document.getElementById('plugin_main').innerHTML=response; } )
    .catch(err => { /*error block*/ });
</script>

@if( sizeof($links) > 0 )
	@php
		$numLinks = sizeof($links);
		if( $numLinks == 1 ) {
			$columns = 1; 	
			$columnsPerLink = 12;	
		} else if( $numLinks == 2 ) {
			$columns = 2; 	
			$columnsPerLink = 6;	
		} else if( $numLinks == 3 ) {
			$columns = 3; 	
			$columnsPerLink = 4;	
		} else if( $numLinks == 4 ) {
			$columns = 4; 	
			$columnsPerLink = 3;	
		} else {
			$columns = 4; 	
			$columnsPerLink = 3;	
		}
	@endphp
	<div class="container" style='margin-top:12px; padding-top:0px;'>
		<div class="row" style='width:100%; text-align:center;'>
		@foreach( $links as $link )
			<div class="col-sm-{!!$columnsPerLink!!}">
				<div class='well' style='text-align:center;'>
					@if( strlen( $link->url ) > 0 )
						@if( strlen( $link->icon ) > 0 )
							<a href='{!!$link->url!!}' target=_blank>
			    	  			<img src='data:image/png;base64, {!!$link->icon!!}' 
									style='float:left; border-radius:4px; width:100%; max-width:200px;'>	
							</a>
						@endif
						<a href='{!!$link->url!!}' target=_blank>
							<div style='font-size:110%;'>{{ $link->title}}</div>
						</a>
						@if( strlen( $link->descr ) > 0 )
							<div style='font-size:100%; font-style:italic;'>{{ $link->descr }}</div>
						@endif					
					@endif
				</div>
			</div>
			@if( $loop->iteration % $columns == 0 )
            	</div>
            	<div class="row">
        	@endif
		@endforeach
		</div>
	</div>
@endif

@if( sizeof($persons) > 0 )
	@php
		$numPersons = sizeof($persons);
		if( $numPersons == 1 ) {
			$columns = 1; 	
			$columnsPerPerson = 12;	
		} else if( $numPersons == 2 || $numPersons == 4 || $numPersons == 6 ) {
			$columns = 2; 	
			$columnsPerPerson = 6;	
		} else {
			$columns = 3;	
			$columnsPerPerson = 4;	
		}
	@endphp
	<div class="container" style='margin-top:12px; padding-top:0px;'>
		<div class="row">
		@foreach( $persons as $person )
			<div class="col-sm-{!!$columnsPerPerson!!}">
				<div class='well' style='text-align:center;'>
					<div style='font-size:110%;'><b>{{ $person->name }}</b>, {{ $person->position }}</div>
	    	  		<img src='data:image/png;base64, {!!$person->icon!!}' style='border-radius:8px; width:100%; max-width:200px;'>	
   					<div style='margin-top:4px; font-size:100%;'>{{ $person->descr }}</div>
				</div>
			</div>
			@if( $loop->iteration % $columns == 0 )
            	</div>
            	<div class="row">
        	@endif
		@endforeach
		</div>
	</div>
@endif

@endsection