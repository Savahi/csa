@extends('layout')

@section('title')
	{{ $supply->title }}
@endsection

@section('pageTitle')
	{{ $supply->title }}
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col-sm-3">
			@if( empty($supply->icon) )
				<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" style='max-height:200px; border-radius:12px;'/>
			@else
				<img src="data:image/jpg;base64, {{$supply->icon}}" style='max-height:200px; border-radius:12px;'/>
			@endif				
		</div>
		<div class = "col-sm-9">	
			<div class="well well-lg">
				<h3>{{ $supply->title }}</h3>
				Дата поставки: {{ $supply->deliver_to}}<br/><br/>
				Описание поставки: {!! $supply->descr !!}<br/>
			</div>
		</div>
	</div>
</div>

<div class="container">
	
@if( count($supply_details) > 0 ) 
	<h3>Культуры</h3>	

	<div class="container">
		@foreach ($supply_details as $sd) 
			<div class="row">
				<div class="col-sm-3">
					@if( empty($sd->icon) )
						<img src="data:image/png;base64, {{config('myconstants.emptyIcon')}}" style='max-height:200px; border-radius:12px;'/>
					@else
						<img src="data:image/jpg;base64, {{$publication->icon}}" style='max-height:200px; border-radius:12px;'/>
					@endif				
				</div>
				<div class = "col-sm-9">	
					<div class="well well-lg">
					<div style='font-weight:bold; font-variant:small-caps; font-size:120%;'>{{ $sd->crop_title }}</div>
					Ферма: <a href='/farm/{{ $sd->farm_id }}'>{{ $sd->farm_title }}</a><br/>
					Культура: <a href='/crop/{{ $sd->crop_id }}'>{{ $sd->crop_title }}</a><br/>				
					Общий объем: {{ $sd->amount_prognosed }}<br/>
					Собрано: {{ $sd->finish_prognosed }}<br/>
					</div>
				</div>
			</div>
		@endforeach
	</div>
@endif

</div>
@endsection
       
