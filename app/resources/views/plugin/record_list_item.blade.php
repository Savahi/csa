			<a href='{{$href}}'>
				<table><tr style='vertical-align:top;'><td>
					@if( empty( {{$icon}} ) )
						<img src="data:image/png;base64, {{Config::get('myconstants.emptyIcon')}}" style='height:100px; border-radius:12px;'/>
					@else
						<img src="data:image/jpg;base64, {{$icon}}" style='height:100px; border-radius:12px;'/>
					@endif					
				</td><td style='padding-left:12px;'>
					<h3>{{ $title }}</h3>
					<div style='font-style:italic;'>{{ $descr }}</div>
				</td></tr></table>
			</a>