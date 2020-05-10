<!DOCTYPE html>
<html lang="en">
<head>
	<title>@yield('title')</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
<link rel="manifest" href="/images/favicon/site.webmanifest">

	<script>
		// These are the basic constants in use...
		var _myConstEmptyIcon = "{{config('myconstants.emptyIcon')}}";
		var _myConstUserCanSuspendSupplyNotLaterThanHours= {{config('myconstants.userCanSuspendSupplyNotLaterThanHours')}};
		var _myConstDebetingForSupplyAllowedNotSoonerThanHours= {{config('myconstants.debetingForSupplyAllowedNotSoonerThanHours')}};
		var _myConstSquareUnit = "{{$htexts['str_square_unit']->title}}";
		var _myConstSquareUnitHTML = "{!!$htexts['html_square_unit']->title!!}";
		var _myConstCurrencyUnit = "{{$htexts['str_currency_unit']->title}}";
		var _myConstCurrencyUnitHTML = "{{ $htexts['html_currency_unit']->title }}";
		var _myConstWeightUnit = "{{ $htexts['str_weight_unit']->title }}";
	@if( Auth::check()  )
		var _myStrName = "{!!$htexts['str_name']->title!!}";
		var _myStrUser = "{!!$htexts['str_user']->title!!}";
		var _myStrUsers = "{!!$htexts['str_users']->title!!}";
		var _myStrContacts = "{!!$htexts['str_contacts']->title!!}";
		var _myStrTitle = "{!!$htexts['str_title']->title!!}";
		var _myStrDescr = "{!!$htexts['str_descr']->title!!}";
		var _myStrResetPassword = "{!!$htexts['str_reset_password']->title!!}";
		var _myStrAmountOfMoney = "{!!$htexts['str_amount_of_money']->title!!}";
		var _myStrDeliveryProblem = "{!!$htexts['str_delivery_problem']->title!!}";
		var _myStrBalance = "{!!$htexts['str_balance']->title!!}";
		var _myPageSupplies = "{!!$htexts['page_supplies']->title!!}";
		var _myPageFarms = "{!!$htexts['page_farms']->title!!}";
		var _myPageCrops = "{!!$htexts['page_crops']->title!!}";
		var _myPageDeliveryPoints = "{!!$htexts['page_delivery_points']->title!!}";
		var _myStrCultivations = "{!!$htexts['str_cultivations']->title!!}";
		var _myStrHarvestings = "{!!$htexts['str_harvestings']->title!!}";
		var _myStrCultivation = "{!!$htexts['str_cultivation']->title!!}";
		var _myStrHarvesting = "{!!$htexts['str_harvesting']->title!!}";
		var _myStrOperations = "{!!$htexts['str_operations']->title!!}";
		var _myStrOperation = "{!!$htexts['str_operation']->title!!}";
		var _myStrCrop = "{!!$htexts['str_crop']->title!!}";
		var _myStrSquare = "{!!$htexts['str_square']->title!!}";
		var _myStrFarm = "{!!$htexts['str_farm']->title!!}";
		var _myStrSupply = "{!!$htexts['str_supply']->title!!}";
		var _myStrPublication = "{!!$htexts['str_publication']->title!!}";
		var _myStrStart = "{!!$htexts['str_start']->title!!}";
		var _myStrFinish = "{!!$htexts['str_finish']->title!!}";
		var _myStrPlan = "{!!$htexts['str_plan']->title!!}";
		var _myStrPrognose = "{!!$htexts['str_prognose']->title!!}";
		var _myStrActual = "{!!$htexts['str_actual']->title!!}";
		var _myStrReserved = "{!!$htexts['str_reserved']->title!!}";
		var _myStrOverall = "{!!$htexts['str_overall']->title!!}";
		var _myStrSuspended = "{!!$htexts['str_suspended']->title!!}";
		var _myStrNotSuspended = "{!!$htexts['str_not_suspended']->title!!}";
		var _myStrNotAssignedToDP  = "{!!$htexts['str_not_assigned_to_dp']->title!!}";
		var _myStrPause = "{!!$htexts['str_pause']->title!!}";
		var _myStrAllow = "{!!$htexts['str_allow']->title!!}";
		var _myStrCreate = "{!!$htexts['str_create']->title!!}";
		var _myStrUpdate = "{!!$htexts['str_update']->title!!}";
		var _myStrDelete = "{!!$htexts['str_delete']->title!!}";
		var _myStrCreating = "{!!$htexts['str_creating']->title!!}";
		var _myStrUpdating = "{!!$htexts['str_updating']->title!!}";
		var _myStrDeleting = "{!!$htexts['str_deleting']->title!!}";
		var _myStrAdd = "{!!$htexts['str_add']->title!!}";
		var _myStrContinue = "{!!$htexts['str_continue']->title!!}";
		var _myStrCancel = "{!!$htexts['str_cancel']->title!!}";
		var _myStrMade = "{!!$htexts['str_made']->title!!}";
		var _myStrMustBeSelected = "{!!$htexts['str_must_be_selected']->title!!}";
		var _myStrMustHaveAValue = "{!!$htexts['str_must_have_a_value']->title!!}";
		var _myStrMustBeNotLess = "{!!$htexts['str_must_be_not_less']->title!!}";
		var _myStrMustBeNotMore = "{!!$htexts['str_must_be_not_more']->title!!}";
		var _myStrAccepted = "{!!$htexts['str_accepted']->title!!}";
		var _myStrFinished = "{!!$htexts['str_finished']->title!!}";
		var _myStrDelivered = "{!!$htexts['str_delivered']->title!!}";
		var _myStrAvailable = "{!!$htexts['str_available']->title!!}";
		var _myStrAmount = "{!!$htexts['str_amount']->title!!}";
		var _myStrDebetings = "{!!$htexts['str_debetings']->title!!}";
		var _myStrDebeting = "{!!$htexts['str_debeting']->title!!}";
		var _myStrDebetingForSupply = "{!!$htexts['str_debeting_for_supply']->title!!}";
		var _myStrRefills = "{!!$htexts['str_refills']->title!!}";
		var _myStrRefilling = "{!!$htexts['str_refilling']->title!!}";
		var _myStrDeposit = "{!!$htexts['str_deposit']->title!!}";
		var _myMsgRevokeRefill = "{!!$htexts['msg_revoke_refill']->title!!}";
		var _myStrPersons = "{!!$htexts['str_persons']->title!!}";
		var _myStrSlides = "{!!$htexts['str_slides']->title!!}";
		var _myStrTexts = "{!!$htexts['str_texts']->title!!}";
		var _myStrLinks = "{!!$htexts['str_links']->title!!}";
		var _myStrDeliveryInfo = "{!!$htexts['str_delivery_info']->title!!}";
		var _myStrPickupInfo = "{!!$htexts['str_pickup_info']->title!!}";
		var _myStrDeliveryPoint = "{!!$htexts['str_delivery_point']->title!!}";
		var _myStrNoData = "{!!$htexts['str_no_data']->title!!}";
		var _myStrAll = "{!!$htexts['str_all']->title!!}";
		var _myStrDepositComment = "{!!$htexts['str_deposit_comment']->title!!}";
		var _lang = "{!!$htexts['lang']!!}";
	@endif

	// Display language choice
	window.addEventListener( 'load', 
		function() {
			lsw = document.getElementById('languageSwitch');
			lsw.innerHTML = "{!!$htexts['lang']!!}";
			lsw.dataset.lang = "{!!$htexts['lang']!!}";
		});

	// Changing language and setting the cookie accordingly
	function languageSwitch(selectLanguageElement) {
		let langs = ['RU', 'EN'];
		let choice = 'RU';
		for( let i = 0; i < langs.length ; i++ ) {
			if( langs[i] === selectLanguageElement.dataset.lang ) {
				if( i < langs.length - 1 )
					choice = langs[i+1];
				else
					choice = langs[0];
				break;
			} 
		}
		document.cookie = "lang=" + choice + "; path=/;";
		//selectLanguageElement.innerHTML = choice;
		window.location.href = window.location;
	}
	</script>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
	<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<script src="/js/a/calendar.js"></script>
	@if( Auth::check() )
		<link href="/css/a/dashboard.css" rel="stylesheet" type="text/css">
		<script src="/js/a/dashboardhelpers.js"></script>
		<script src="/js/a/dashboardrequest.js"></script>
		<script src="/js/a/dashboarddata.js"></script>
		<script src="/js/a/dashboardicon.js"></script>
		<script src="/js/a/udashboard.js"></script>
		<script src="/js/a/udashboardactions.js"></script>
		@if ( Auth::user()->admin_privilegies > 0 )
			<script src="/js/a/adashboard.js"></script>
			<script src="/js/a/adashboardactions.js"></script>
			<script src="/js/a/edashboard.js"></script>
			<script src="/js/a/edashboardactions.js"></script>
		@elseif( Auth::user()->farm_admin > 0 ) 		<!-- A farmer -->
			<script src="/js/a/fdashboard.js"></script>
			<script src="/js/a/fdashboardactions.js"></script>
		@elseif( Auth::user()->sorting_station_admin > 0 ) 	<!-- A Sorting Station admin -->
		@elseif( Auth::user()->delivery_unit_admin > 0 ) 	<!-- A Delivery Unit admin -->
			<script src="/js/a/ddashboard.js"></script>
			<script src="/js/a/ddashboardactions.js"></script>
		@endif	
		@if( Auth::user()->delivery_point_admin > 0 ) 		<!-- A user - a delivery point admin -->
		@endif
	@endif	
	
	@yield('head_extra')

	<link href="/css/layout.css" rel="stylesheet" type="text/css">

</head>

<body id="top" data-offset="50" style='margin:0; padding:0;'>

<nav class='navbar'>
	<div class="dropdown">
		<button class="dropdown-btn">&#9776;</button>
		<div class="dropdown-content">
			<div style='margin:0; padding:2px; border-bottom:1px dotted #7f7f7f;'>
	    	    <a href="/participation">{{ $htexts['page_participation']->title }}</a>
		    	<a href="/questions_and_answers">{{ $htexts['page_faq']->title }}</a>
			</div>
			<div style='margin:0; padding:2px; border-bottom:1px dotted #7f7f7f;'>
	        	<a href="#contact" style='font-size:130%;'>
					<!--<span class="glyphicon glyphicon-send"></span><span class="glyphicon glyphicon-envelope"></span>-->
					{{ $htexts['page_contact']->title }}</a>
				<a href="/user" style='font-size:130%;'>
					<!--<span class='glyphicon glyphicon-log-in'></span><span class='glyphicon glyphicon-user'></span>-->
					{{ $htexts['page_login']->title }}</a>
			</div>
			<div style='margin:0; padding:2px; border-bottom:1px dotted #7f7f7f;'>
        		<a href="/supply" style='font-size:130%;'>
					<!--<span class="glyphicon glyphicon-shopping-cart"></span>-->
					{{ $htexts['page_supplies']->title }}</a>
        		<a href="/delivery_point" style='font-size:130%;'>
					<!--<span class="glyphicon glyphicon-map-marker"></span><span class="glyphicon glyphicon-shopping-cart"></span>-->
					{{ $htexts['page_delivery_points']->title }}</a>
	        	<a href="/workflow/all" style='font-size:130%;'>
					{{ $htexts['page_workflow']->title }}</a>
	        	<a href="/farm" style='font-size:130%;'>
					<!--<span class="glyphicon glyphicon-grain"></span><span class="glyphicon glyphicon-user"></span>-->
					{{ $htexts['page_farms']->title }}</a>
        		<a href="/crop" style='font-size:130%;'>
					<!--<span class="glyphicon glyphicon-grain"></span><span class="glyphicon glyphicon-grain"></span>-->
					{{ $htexts['page_crops']->title }}</a>
	        	<a href="/map" style='font-size:130%;'>
					<!--<span class="glyphicon glyphicon-map-marker"></span><span class="glyphicon glyphicon-map-marker"></span>-->
					{{ $htexts['page_map']->title }}</a>
			</div>
	        <a href="/publication" style='font-size:130%;'>
				<!--<span class="glyphicon glyphicon-book"></span><span class="glyphicon glyphicon-pencil"></span>-->
				{{ $htexts['page_publications']->title }}</a>
		</div>
	</div>
	<!-- Login / Logout -->
	&nbsp;
	@if( Auth::check()  )
		<a href='/user'><span class="glyphicon glyphicon-user"></span></a>
		<a class="dropdown-item" href="{{ route('logout') }}"
			onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
			<span class="glyphicon glyphicon-log-out"></span>
		</a>
		<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
			@csrf
		</form>
	@else
		<a href='/login'><span class="glyphicon glyphicon-log-in"></span></a>
	@endif

	&nbsp;&nbsp;<a href="/" title='Главная страница'><!--&#10224;-->{{$htexts['site']->title}}</a>
	<!--&nbsp;&nbsp;<a href="#top">&#129033;</a>-->

	<span id='languageSwitch' onclick='languageSwitch(this);' data-lang='RU' 
		style='cursor:pointer; padding:2px 2px 2px 4px; border:1px dotted #7f7f7f; border-radius:4px; color:white; font-size:14px;'>RU</span>
</nav>

<div style='position:relative; box-sizing:border-box; width:100%; padding:50px 0px 0px 0px; clear:both;'>
	@if (trim($__env->yieldContent('pageTitle')))
    	<div id='pageTitle'>@yield('pageTitle')</div>
	@endif
    @yield('content')
</div>

<!-- Container (Contact Section) -->
<div id="contact" class="container" style='margin-top:8px; padding-top:8px; border-top:1px dotted lightgray;'>
  <div style='margin:2px 0px 18px 0px; padding:2px 0px 2px 0px; text-align:center; font-size:110%;'>
	{{ $htexts['page_contact']->title }}
  </div>
  <div class="row">
    <div class="col-md-3">
      <p></p>
      <!--<p><span class="glyphicon glyphicon-phone"></span>+7 (900) 0000000</p>-->
      <p>
		<span class="glyphicon glyphicon-map-marker"></span>
		<span class="glyphicon glyphicon-map-marker"></span>
		<a href='/map'>{{ $htexts['page_map']->title }}</a></p>
    </div>
    <div class="col-md-9">
		@include('plugin/contact_form');
    </div>
  </div>
</div>

<!-- Image of location/map -->
<!--
	<img src="map.jpg" class="img-responsive" style="width:100%">
-->

<!-- Footer -->
<footer class="text-center">
  <a href="#top" title="TO TOP">
    <span class="glyphicon glyphicon-chevron-up"></span>
  </a>
</footer>

<script>
</script>

<div id='myBlackoutDiv' style='position:absolute; display:none; left:0; top:0; min-width:100%; min-height:100%; background-color:#4f4f4f; opacity:0.35;'></div>
<div id='myPopupDiv' style='display:none; position:absolute; left:20%; top:20%; width:60%; height:60%; border-radius:20px; background-color:white;'>
  <div id='myPopupDivHeaderAndBody' style='box-sizing:border-box; width:96%; margin:0; overflow-x:auto; overflow-y:auto;'> 
	<div id='myPopupDivHeader' style='box-sizing:border-box; width:100%; margin:0px; padding:14px; font-size:18px; font-weight:bold;'></div>
	<div id='myPopupDivBody' 
		style='width:100%; box-sizing:border-box; margin:0; border-top:1px solid lightgray; padding:12px 0px 0px 0px; background-color:#ffffff;'></div>
  </div>
	<div id='myPopupDivFooter' 
		style='height:50px; width:100%; margin:0px; box-sizing:border-box; border-top:1px solid lightgray; padding:12px 24px 12px 24px;'>
		<div id='myPopupDivErrorMessage' style='width:100%; text-align:center; color:red;'></div>
		<button type='button' class='btn btn-default' id='myPopupDivOk' 
			style='height:44px; margin:0px 12px 0px 12px; padding-left:20px; padding-right:20px; float:left;'>
			<span class='glyphicon glyphicon-ok'></span></button>
		<button type='button' class='btn btn-default' id='myPopupDivClose' 
			style='height:44px; margin:0px 12px 0px 12px; padding-left:20px; padding-right:20px; float:left;'>
			<span class='glyphicon glyphicon-remove'></span></button>
	</div>
</div>

<script>

var _myBlackoutDiv = document.getElementById('myBlackoutDiv');
var _myPopupDiv = document.getElementById('myPopupDiv');
var _myPopupDivYOffset; // = Math.floor( (window.innerHeight-50)*0.2 );
var _myPopupDivHeight; // = (window.innerHeight-50)*0.8;
var _myPopupDivXOffset; // = window.innerWidth*0.2;
var _myPopupDivWidth; // = window.innerWidth*0.8;

function myPopupDivShow(header, text=null, properties={} ) {	// mode: 0 - no buttons, 1 - close button, 2 - ok button, 4 - both buttons
	let mode = ('mode' in properties) ? properties.mode : -1;
	let size = ('size' in properties) ? properties.size: 's';
	let okh = ('okh' in properties) ? properties.okh : null;
	let okharg = ('okharg' in properties) ? properties.okharg : null;

	_myBlackoutDiv = document.getElementById('myBlackoutDiv');
	_myPopupDiv = document.getElementById('myPopupDiv');
	
	let top = window.pageYOffset || document.documentElement.scrollTop
	_myBlackoutDiv.style.top = top.toString() + 'px';

	myPopupDivSetDimensions( size );

	_myBlackoutDiv.style.display='block';	
	if( mode < 0 ) {
		return;
	}
	//let div = document.getElementById('myPopupDiv');	
	_myPopupDiv.style.display='block';
	document.getElementById('myPopupDivHeader').innerHTML=header;
	document.getElementById('myPopupDivBody').innerHTML=text;
	myPopupDivSetMode(mode, okh, okharg);
}

function myPopupDivBody() {
	return document.getElementById('myPopupDivBody');
}

function myPopupDivSetBody(text) {
	document.getElementById('myPopupDivBody').innerHTML = text;	
}

function myPopupDivSetDimensions(size='s') {
	let margin = (size ==='s') ? 0.25 : 0.1;
	let top = window.pageYOffset || document.documentElement.scrollTop;
	_myPopupDivYOffset = Math.floor( (window.innerHeight - 50) * margin );
	_myPopupDivHeight = Math.floor( (window.innerHeight - 50) * (1.0 - margin*2.0) );
	_myPopupDivXOffset = Math.floor( window.innerWidth * margin );
	_myPopupDivWidth = Math.floor( window.innerWidth * (1.0 - margin*2.0) );
	_myPopupDiv.style.top = (top + _myPopupDivYOffset).toString() + 'px';
	_myPopupDiv.style.left = (_myPopupDivXOffset).toString() + 'px';
	_myPopupDiv.style.height = (_myPopupDivHeight + 50).toString() + 'px';
	_myPopupDiv.style.width = (_myPopupDivWidth).toString() + 'px';
	//document.getElementById('myPopupDivHeader').style.height = '60px';
	document.getElementById('myPopupDivHeaderAndBody').style.height = (_myPopupDivHeight - 40).toString() + 'px';
	document.getElementById('myPopupDivFooter').style.height = '40px';
}


function myPopupDivSetMode(mode, okh=null, okharg=null) {
	let bfooter = document.getElementById('myPopupDivFooter');
	bfooter.style.display = ( mode == 0 ) ? 'none' : 'block';

	let bclose = document.getElementById('myPopupDivClose');
	if( mode == 1 || mode == 4 ) {
		bclose.onclick = function(e) { myPopupDivHide(); }
		bclose.style.display = 'block';
	} else {
		bclose.onclick = null;
		bclose.style.display = 'none';
	}

	let bok = document.getElementById('myPopupDivOk');
	if( mode == 2 || mode == 4 ) {
		bok.onclick = function(e) { okh( okharg ); }
		bok.style.display = 'block';
	} else {
		bok.onclick = null;
		bok.style.display = 'none';
	}
}

function myPopupDivHide(title, text) {
	document.getElementById('myBlackoutDiv').style.display='none';
	let div = document.getElementById('myPopupDiv');	
	if( div.style.display !== 'none' ) {
		document.getElementById('myPopupDiv').style.display='none';
		document.getElementById('myPopupDivHeader').innerHTML='';
		let container = document.getElementById('myPopupDivBody');
		while(container.hasChildNodes()) {
			container.removeChild(container.lastChild);	
		}
		container.innerHTML=null;
		document.getElementById('myPopupDivOk').disabled = false;
		document.getElementById('myPopupDivErrorMessage').innerHTML = '';
		myPopupDivSetMode(0);
	}
}

function myFormatNumberBy3( number ) {
  return number.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1'+'&nbsp;');
}

</script>

</body>
</html>
