@extends('layout')

@section('title')
	{{ $htexts['page_login']->title }} 
@endsection

@section('content')

<div class="" style='margin-top:20px; margin-bottom:0px; padding-top:0px; padding-bottom:0px;'>
	<div class="col-sm-6">
        	@if (session('status'))
            	<div class="alert alert-success" role="alert">
                	{{ session('status') }}
				</div>
			@endif

			<div class="well well-sm">
				<span class="glyphicon glyphicon-envelope a-dashboard-icon" title='Email'></span> {{Auth::user()->email}}
				@if ( !Auth::user()->email_verified_at )
					<span style="color:red" title='The email is not verified'>(!)</span>
				@endif
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span class='a-dashboard-icon' title='Reset your password'>
						<span class="glyphicon glyphicon-option-horizontal" style='margin-right:0; padding-right:0;'></span>						
						<span class="glyphicon glyphicon-option-horizontal" style='margin-left:-4px; padding-left:0px;'></span>						
					</span>
				<a href='/user_reset_password' title='{{$htexts["str_reset_password"]->title}}'>
					<span class="glyphicon glyphicon-refresh" style='margin-left:8px;'></span>&nbsp;{{$htexts["str_reset_password"]->title}}						
				</a>
			</div> 	
    </div>
	<div class="col-sm-6">
		<div class='well well-sm' style='padding-bottom:2px;'>
			<table style='box-sizing:border-box; width:100%;'><tr style='vertical-align:top;'><td style='width:70px;'>
				@if( is_null(Auth::user()->icon) || empty(Auth::user()->icon) )
					<span class="glyphicon glyphicon-user a-dashboard-icon"></span>
				@else
					<div style='display:inline-block; position:relative; top:-4px;'><img src="data:image/png;base64,{!!Auth::user()->icon!!}" 
						style='height:54px; border-radius:4px; margin:0px 4px 0px 4px;'></div>
				@endif
			</td><td>
			<span class="glyphicon glyphicon-pencil" style='cursor:pointer;' onclick='uDashboardActionUpdateUserData( {{Auth::user()}} );'></span>
			<span id='aDashboardUserName'>{{ Auth::user()->name }}</span>&nbsp;&nbsp;
				<a class="dropdown-item" href="{{ route('logout') }}"
					onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
					<span class="glyphicon glyphicon-log-out"></span>
					<!--{{ __('Exit') }}-->{!!$htexts['str_exit']->title!!}				
				</a>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					@csrf
				</form>
			<div id='aDashboardUserContacts' style='margin-top:4px;'>
				@if( !is_null(Auth::user()->contacts) && !empty(Auth::user()->contacts) )
					{{ Auth::user()->contacts }}
				@endif
			</div>
			</tr></table>
		</div>
	</div>
	<!--
	<div class="col-md-8" style='margin-bottom:0px; padding-bottom:0px;'>
		Here is the place for user's to view their messages, payments, balancies etc.
	</div>
	-->
</div>

<div style='width:100%; box-sizing:border-box; margin:0px; padding:14px;'>
<!--	<div class="col-md-12" style='margin:0px 12px 12px 12px; padding:4px;'>-->
		@if ( Auth::user()->admin_privilegies > 0 ) 	<!-- The Admin -->
			@include('admin')
		@elseif( Auth::user()->farm_admin > 0 ) 		<!-- A farmer -->
			@include('fadmin')
		@elseif( Auth::user()->delivery_unit_admin > 0 ) 	<!-- A Delivery Unit admin -->
			@include('dadmin')
		@elseif ( Auth::user()->delivery_point_id <= 0 ) 	<!-- A registered user not attached to any delivery point -->
			@include('noadmin')
		@else 												<!-- An ordinary user or a delivery point admin -->
			@include('uadmin')
		@endif	
<!--	</div>-->
<div id='aDashboardDataTitleContainer' style='background-color:#dfdfdf;'>
	<div>
		<div id='aDashboardRequestBack' class='a-dashboard-back-button-disabled'
			style='border-radius:4px; display:inline-block; margin-right:12px; padding:0px 8px 0px 8px;' 
			onclick='aRequestStackBack();'>&lt;</div>
		<div id='aDashboardDataTitle' style='display:inline-block;'><!--&#8593;&nbsp;<i><span style='font-weight:normal;'>Please, make your choice...</span></i>&nbsp;&#8593;-->&nbsp;</div>
	</div>
	<div id='aDashboardDataFilter' 
		style='box-sizing:border-box; color:#2f2f2f; margin:4px 0px 0px 0px; padding:4px 4px 2px 4px; display:none;'>

		<div id='aDashboardDataFilterPagination' style='display:inline-block; border:0px dotted gray; border-radius:4px; margin-right:24px; padding:4px;'> 
			<span onclick="aDashboardFilterPage(-1);"
				style='cursor:pointer; border:1px dotted lightgray; background-color:#efefef; border-radius:4px; padding:1px 4px 1px 4px;'>&lt;</span>
			<span onclick="aDashboardFilterPage(1);" 
				style='cursor:pointer; border:1px dotted lightgray; background-color:#efefef; border-radius:4px; padding:1px 4px 1px 4px;'>&gt;</span>
			<span id="aDashboardFilterOffset" data-filter='offset'>1</span>-<span id="aDashboardFilterOffsetPlusLimit" data-filter='offsetPlusLimit'>50</span> (<span id="aDashboardFilterTotal">55</span>)
		</div>
		<div id='aDashboardDataFilterElements' style='display:inline-block; padding:2px;'>
			<button id='aDashboardDataFilterButton' style='cursor:pointer; border:1px solid gray; border-radius:4px;'>
				<span class="glyphicon glyphicon-search"></span>
			</button>
			<span id='aDashboardDataFilterInputs' style='padding:2px;'></span>
		</div>
	</div>
</div>

<div id='aDashboardDataArray' style='width:100%; box-sizing:border-box; margin:12px 0px 0px 0px; padding:0;'>
</div>

</div>

<script>
	window.addEventListener('load', function() {
		aRequestStackSetBackButton( 'aDashboardRequestBack' );

		let items = document.querySelectorAll('[data-toggle]');
		for( let i = 0 ; i < items.length ; i++ ) {
			let it = items[i];
			aRequestStackAddSection( it.href );
			it.addEventListener( 'click', function(e) { 
				aRequestStackSetActiveSection( it.href );
				if( 'defaultsubmenuid' in it.dataset ) {
					aRequestStackRunLatestOrDefault( document.getElementById(it.dataset.defaultsubmenuid).onclick );
				}
			});
			if( i === 0 ) {
				aRequestStackSetActiveSection(it.href);
			}
		}
	} );	
</script>


@endsection
