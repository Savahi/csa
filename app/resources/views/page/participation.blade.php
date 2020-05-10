@extends('layout')

@section('title')
	{{ $htexts['page_participation']->title }}
@endsection

@section('pageTitle')
	{{ $htexts['page_participation']->title }}
@endsection

@section('content')

<div class='container'>

<h3>{{ $htexts['rules']->title }}</h3>
{!! $htexts['rules']->descr !!}

<h3 style='margin-top:24px;'>{{ $htexts['join']->title }}</h3>
{!! $htexts['join']->descr !!}

<h3 style='margin-top:24px;'>{{ $htexts['payment']->title }}</h3>
{!! $htexts['payment']->descr !!}

</div>

@endsection