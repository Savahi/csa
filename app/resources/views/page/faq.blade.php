@extends('layout')

@section('title')
	{{ $htexts['page_faq']->title }}
@endsection

@section('pageTitle')
	{{ $htexts['page_faq']->title }}
@endsection

@section('content')
	<div class="container" style='margin-top:24px; padding-top:0px;'>
		@foreach( $faq as $q )
			<p><a href="#{!!$loop->iteration!!}">{{$q->question}}</a></p>
		@endforeach

		@foreach( $faq as $q )
			<a name='{!!$loop->iteration!!}' style='padding:50px;'>&nbsp;</a>
			<h3>{{$q->question}}</h3>
			<div class='well'>
				{!!$q->answer!!}<br/>
			<a href='#top'>&#129033;<!-- &#8593; &#10514; --></a>
			</div>
		@endforeach
	</div>
@endsection