<div class="row" align=center>
	@php
		$maxSupplies = 1;
		$maxPublications = 3;
		$numSupplies = count($supplies);
		if( $numSupplies > $maxSupplies )
			$numSupplies = $maxSupplies;

		$numPublications = count($publications);
		if( $numPublications > $maxPublications )
			$numPublications = $maxPublications;

		$imageWidth = '25%';
		$numColumnsPerRow = 0;
		$totalSuppliesAndPublications = $numSupplies + $numPublications;
		if( $totalSuppliesAndPublications == 1 ) {
			$numColumnsPerRow = 12;
			$imageWidth = '22%';
		} else if( $totalSuppliesAndPublications == 2 ) {
			$numColumnsPerRow = 6;
			$imageWidth = '44%';
		} else if( $totalSuppliesAndPublications == 3 ) {
			$numColumnsPerRow = 4;
			$imageWidth = '60%';
		} else if( $totalSuppliesAndPublications == 4 ) {
			$numColumnsPerRow = 3;
			$imageWidth = '80%';
		}
	@endphp

	@for( $i = 0 ; $i < $numSupplies ; $i++ ) 
	<div class="col-sm-{{ $numColumnsPerRow }}">
		<a href="/supply/{{ $supplies[$i]->id }}">
			<div class="text-center"><strong>Поставка {{$supplies[$i]->deliver_to}}</strong><br/>
				<img src="data:image/png;base64, {{$supplies[$i]->icon}}" class="img-rounded" 
					alt="" style='width:{{$imageWidth}}; height:auto;'>
			</div>
		</a>
		<div>
			<b>{{$supplies[$i]->title}}</b><br/>
			<i>{{$supplies[$i]->descr}}</i><br/>
			[ <a href='/supply'>План поставок + архив</a> ]
		</div>
	</div>
	@endfor

	@for( $i = 0 ; $i < $numPublications ; $i++ ) 
	<div class="col-sm-{{ $numColumnsPerRow }}">
		<a href="/publication/{{ $publications[$i]->id }}">
			<div class="text-center"><strong>{{$publications[$i]->title}}</strong><br/>
				<img src="data:image/png;base64, {{$publications[$i]->icon}}" class="img-rounded" alt="" style='width:80%; height:auto;'>
			</div>
		</a>
		<div>
			<i>{{$publications[$i]->descr}}</i>
		</div>
	</div>
	@endfor
</div>
