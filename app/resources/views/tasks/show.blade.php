@extends('layout')

@section('pageTitle')
	TASK {{ $task->id }}
@endsection

@section('title')
	TASK {{ $task->id }}
@endsection

@section('content')
	<h1>Task {{ $task->id }}</h1>
	<h4>{{$task->descr}}</h4>
@endsection
