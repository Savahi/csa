@extends('layout')

@section('pageTitle')
	{{ $htexts['page_workflow']->title }} 
@endsection

@section('title')
	{{ $htexts['page_workflow']->title }} 
@endsection

@section('head_extra')

@endsection

@section('content')
	
	@include('workflow.agantt');

@endsection
       
