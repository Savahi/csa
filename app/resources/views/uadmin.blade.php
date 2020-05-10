<script>
	var _uDashboardDeliveryPointData=null;

	function uRequestDeliveryPointDataForUserDashboard( delivery_point_id, destElem ) { 
		let url = `/u_delivery_point/${delivery_point_id}`;
		fetch(url).then(data=>data.json()).then( function(data) { 
			destElem.innerHTML = `${_myStrDeliveryPoint}: ` + 
				`<a href='/delivery_point/${delivery_point_id}' target=_blank><b>${data[0].title}</b>, ${data[0].address}</a>.`;
			if( data[0].cnt_users !== null && data[0].cnt_users !== '' ) { 		// The user is a deliver point admin
				destElem.innerHTML += ` ${_myStrUsers}: ${data[0].cnt_users}.`; 
			} else if( data[0].admin_email !== null &&  data[0].admin_email !== '' ) { 		// The "normal" user
				destElem.innerHTML += `<br/>{{$htexts["str_admin"]->title}}: <b>${data[0].admin_name}, ${data[0].admin_email}, ${data[0].admin_contacts}.</b>`; 
			}
			if( data[0].pickup_info !== null && data[0].pickup_info !== '' ) {
				destElem.innerHTML += `<br/>${_myStrPickupInfo}:&nbsp;<b><i>${data[0].pickup_info}</i></b>`; 
			}
			if( data[0].delivery_info !== null && data[0].delivery_info !== '' ) {
				destElem.innerHTML += `<br/>${_myStrDeliveryInfo}:&nbsp;<b><i>${data[0].delivery_info}</i></b>`; 
			}
			_uDashboardDeliveryPointData = { id:data[0].id, title:data[0].title, address:data[0].address, descr:data[0].descr,
				latitude:data[0].latitude, longitude:data[0].longitude,				
				pickup_info:data[0].pickup_info, delivery_info:data[0].delivery_info };
		});
	}
	
	function uDashboardActionEditDeliveryPoint() {
		if( _uDashboardDeliveryPointData !== null ) {
			uDashboardActionUpdateDeliveryPoint( _uDashboardDeliveryPointData );
		}
	}
</script>

<h3>User's</h3>
<div class="" style='box-sizing:border-box; margin:0px; padding:0px;'>
	<div class='row' style='box-sizing:border-box; margin:0px; padding:0px;'>
		<div class='col-sm-6' style='box-sizing:border-box; margin:0px; padding:0px;'>
			<div class='well' style='padding:12px;'>
				<div style='float:left; margin-top:4px; margin-bottom:4px; margin-right:48px;'>
					<span class="glyphicon glyphicon-usd a-dashboard-icon" title='{{$htexts["str_balance"]->title}}'></span>
					{!!$htexts['str_balance']->title!!}:
					<span style='font-weight:bold;'>{{Auth::user()->balance}}</span>
					<i>{!!$htexts['str_currency_unit']->title!!}</i>
				</div>
				<div style='float:left; margin-top:4px; margin-bottom:4px; margin-right:48px;'>
					<span class="glyphicon glyphicon-usd a-dashboard-icon" title='{{$htexts["str_deposit"]->title}}'></span>
					{!!$htexts['str_deposit']->title!!}:
					<span style='font-weight:bold;'>{{Auth::user()->deposit}}</span>
					<i>{!!$htexts['str_currency_unit']->title!!}</i>
				</div>
				<div style='float:left; margin-top:4px; margin-bottom:4px; margin-right:48px;'>
					<div class='a-dashboard-icon' style='float:left;' title='{{$htexts["page_workflow"]->title}}'>
						<span class="glyphicon glyphicon-grain"></span>
						+
						<span class="glyphicon glyphicon-shopping-cart"></span>
					</div>
					<a href='/workflow/all/0' target=_blank>
						<span class="glyphicon glyphicon-new-window" style='margin-left:4px;'></span>
					</a>
				</div>	
				<div style='float:left; margin-top:4px; margin-bottom:4px;'>
					<div class='a-dashboard-icon' style='float:left; margin-right:4px;' title='{{$htexts["str_not_suspended"]->title}}'>
						<span class="glyphicon glyphicon-shopping-cart"></span>
						<span class="glyphicon glyphicon-thumbs-up"></span>
					</div>
					{{$htexts['str_not_suspended']->title}}?
					<span id='uDashboardSuspended' onclick='uDashboardActionSwitchSuspend({{Auth::user()}}, this)'>&nbsp;</span>
				</div>
				<div style='clear:both;'></div>
			</div>
		</div>
		<div class='col-sm-6'>
			<div class='well' style='padding:12px;'>
				@if( Auth::user()->delivery_point_admin > 0 ) 	<!-- A Delivery Point admin -->
					<span class="glyphicon glyphicon-pencil" style='cursor:pointer;'
						onclick='uDashboardActionEditDeliveryPoint();'></span>				
				@endif
				<span class="glyphicon glyphicon-map-marker a-dashboard-icon"></span>
				<span id='uDashboardPickupDetails'></span>
				<script>
					uRequestDeliveryPointDataForUserDashboard( {{Auth::user()->delivery_point_id}}, 
						document.getElementById('uDashboardPickupDetails') );
				</script>
			</div>
		</div>
	</div>
</div>

<div>
	<script>
		let el = document.getElementById('uDashboardSuspended');
		el.className = ({{Auth::user()->is_suspended_for_supply}}==1) ? 'glyphicon glyphicon-unchecked' : 'glyphicon glyphicon-check';
		el.dataset.is_suspended_for_supply = ({{Auth::user()->is_suspended_for_supply}}==1) ? '1' : '0';
	</script>

	<ul class="nav nav-tabs" style='width:100%;'>
		<li class="active" title='Your supplies'>
			<a data-toggle="tab" href="#u_supplies" data-defaultsubmenuid="uDashboardLinkSupplies">
				<span class="glyphicon glyphicon-shopping-cart a-dashboard-menu"></span></a>
		</li>
    	<li title='Your money: refills and debetings'>
			<a data-toggle="tab" href="#u_money" data-defaultsubmenuid="uDashboardLinkRefills">
				<span class="glyphicon glyphicon-usd a-dashboard-menu"></span></a>
		</li>
		@if( Auth::user()->delivery_point_admin > 0 ) 	<!-- A Delivery Point admin -->
	    <li title='Your delivery point: the users'>
			<a data-toggle="tab" href="#u_users" data-defaultsubmenuid="uDashboardUsers">
				<span class="glyphicon glyphicon-user a-dashboard-menu" style='margin-right:0; padding-right:0;'></span>
				<span class="glyphicon glyphicon-user a-dashboard-menu" style='margin-left:0; padding-left:0;'></span></a>
		</li>
		@endif
 	 </ul>

	<div class="tab-content" style='width:100%;' id="uDashboardLinks">
		<div id="u_supplies" class="tab-pane fade in active">
			<span id='uDashboardLinkSupplies' data-dashboardmenu='u_supplies' 
				onclick="uLoadSupplies(document.getElementById('uDashboardLinkSupplies'));">
				{{ $htexts['str_pending_supplies']->title }}</span>&nbsp;|
			<span onclick="uLoadSupplies(this,'/u_supplies/delivered');" data-dashboardmenu='u_supplies'>
				{{ $htexts['str_delivered_supplies']->title }}</span>
		</div>
		<div id="u_money" class="tab-pane fade">
			<span id='uDashboardLinkRefills' data-dashboardmenu='u_money'
				onclick="uLoadRefills(document.getElementById('uDashboardLinkRefills'));">
				{{ $htexts['str_refills']->title }}</span>&nbsp;|
			<span onclick="uLoadDebetings(this);" id='uDashboardLinkDebetings' data-dashboardmenu='u_money'>
				{{ $htexts['str_debetings']->title }}</span>
		</div>
		@if( Auth::user()->delivery_point_admin > 0 ) 	<!-- A Delivery Point admin -->
		<div id="u_users" class="tab-pane fade">
			<span id='uDashboardUsers' data-dashboardmenu='u_money' 
				onclick="uLoadUsers(document.getElementById('uDashboardUsers'));">{{ $htexts['str_users']->title }}	
			</span>
		</div>
		@endif
	</div>
</div>

