@extends('layout')

@section('pageTitle')
	TASKS
@endsection


@section('title')
	TASKS
@endsection

@section('content')
	<ul>
		@foreach( $tasks as $task )
			<li>
				<a href='/tasks/{{$task->id}}'>
					{{ $task->descr }}</li>
				</a>
			</li>
		@endforeach
	</ul>		
@endsection

</body>

</html>