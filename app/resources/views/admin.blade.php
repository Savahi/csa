<h3>Admin's</h3>
<div class="container" style='width:100%; box-sizing:border-box; margin:0px; padding:0px;'>
	<div class='row' style='box-sizing:border-box; margin:0px; padding:0px;'>
		<div class='col-sm-3'>
			<div class='well' style='padding:12px;'>
				{!!$htexts['str_users']->title!!}: 
				<span id='aDashboardUsersCount' data-count='{{$users_attached_to_delivery_point}}'>
					<b>{{$users_attached_to_delivery_point}}</b>&nbsp;/ ({{$users_registered}} {!!$htexts['str_overall']->title!!})</span><br/>
				{!!$htexts['str_balance']->title!!} > 0:&nbsp;&nbsp; 
				<span id='aDashboardUsersWithNon0BalanceCount' data-count='{{$users_with_nonzero_balance}}'><b>{{$users_with_nonzero_balance}}</b></span>
			</div>
		</div>
		<div class='col-sm-3'>
			<div class='well' style='padding:12px;'>
				{!!$htexts['str_balance']->title!!} 
					({!!$htexts['str_overall']->title!!})&nbsp;&nbsp;<b>{{ number_format( $total_balance, 2, ".", "," ) }}</b><br/>
				{!!$htexts['str_deposit']->title!!} 
					({!!$htexts['str_overall']->title!!})&nbsp;&nbsp;<b>{{ number_format( $total_deposit, 2, ".", "," ) }}</b>
			</div>
		</div>
		<div class='col-sm-3'>
			<div class='well' style='padding:12px;'>
				{!!$htexts['page_delivery_points']->title!!}:&nbsp;&nbsp;<b>{{$delivery_points_count}}</b><br/>
				{!!$htexts['page_farms']->title!!}:&nbsp;&nbsp;<b>{{$farms_count}}</b>
			</div>
		</div>
		<div class='col-sm-3'>
			<div class='well' style='padding:12px;'>
				<a href='/workflow/all/0' target=_blank>{!!$htexts['page_workflow']->title!!}</a><br/>
			</div>
		</div>
	</div>
</div>

<div class="container" style='width:100%; box-sizing:border-box; margin:0px; padding:0px;'>
  <ul class="nav nav-tabs" style='width:100%;'>
    <li class="active"><a data-toggle="tab" href="#a_operationalmanagement" data-defaultsubmenuid="aDashboardLinkCultivations">
		{!!$htexts['str_operational_management']->title!!}</a></li>
    <li><a data-toggle="tab" href="#a_people" data-defaultsubmenuid="aDashboardLinkUsers">{!!$htexts['str_account_management']->title!!}</a></li>
    <li><a data-toggle="tab" href="#a_infrastructure" data-defaultsubmenuid="aDashboardLinkFarms">
		{!!$htexts['page_farms']->title!!} & {!!$htexts['page_delivery_points']->title!!}</a></li>
    <li><a data-toggle="tab" href="#a_editor" data-defaultsubmenuid="aDashboardLinkPublications">
		<span class="glyphicon glyphicon-cog"></span> & {!!$htexts['page_publications']->title!!}</a></li>
  </ul>

  <div class="tab-content" style='width:100%;' id="aDashboardLinks">
    <div id="a_operationalmanagement" class="tab-pane fade in active">
		<span onclick="aLoadCrops(document.getElementById('aDashboardLinkCrops'));" id='aDashboardLinkCrops' 
			data-dashboardmenu='a_operationalmanagement'>{!!$htexts['page_crops']->title!!}</span>&nbsp;|
		<span onclick="aLoadCultivationAssignments(document.getElementById('aDashboardLinkCultivations'));" id='aDashboardLinkCultivations' 
			data-dashboardmenu='a_operationalmanagement'>{!!$htexts['str_cultivations']->title!!}</span>&nbsp;|
		<span onclick='aLoadHarvestingAssignments(this);' data-dashboardmenu='a_operationalmanagement'>{!!$htexts['str_harvestings']->title!!}</span>&nbsp;|
		<span onclick='aLoadSupplies(this);' data-dashboardmenu='a_operationalmanagement'>{!!$htexts['page_supplies']->title!!}</span>
    </div>
    <div id="a_people" class="tab-pane fade">
		<span onclick="aLoadUsers(document.getElementById('aDashboardLinkUsers'))" id='aDashboardLinkUsers' 
			data-dashboardmenu='a_people'>{!!$htexts['str_users']->title!!}</span>&nbsp;|
		<span onclick="aLoadUsers(this,'delivery_point_admin');" data-dashboardmenu='a_people'>{!!$htexts['page_delivery_points']->title!!}</span>&nbsp;|
		<span onclick="aLoadUsers(this,'farm_admin');" data-dashboardmenu='a_people'>{!!$htexts['page_farms']->title!!}</span>&nbsp;|
		<span onclick="aLoadUsers(this,'delivery_unit_admin');" data-dashboardmenu='a_people'>{!!$htexts['str_logistics']->title!!}</span>&nbsp;|
		<span onclick="aLoadUsers(this,'not_assigned_to_dp');" data-dashboardmenu='a_people'>{!!$htexts['str_not_assigned_to_dp']->title!!}</span>&nbsp;|
		<span onclick="aLoadUsers(this,'all');" data-dashboardmenu='a_people'>{!!$htexts['str_all']->title!!}</span>
    </div>
    <div id="a_infrastructure" class="tab-pane fade">
		<span onclick="aLoadFarms(document.getElementById('aDashboardLinkFarms'));" id='aDashboardLinkFarms' 
			data-dashboardmenu='a_infrastructure'>{!!$htexts['page_farms']->title!!}</span>&nbsp;|
		<span onclick="aLoadDeliveryPoints(this);" data-dashboardmenu='a_infrastructure'>{!!$htexts['page_delivery_points']->title!!}</span>
		<!--<span onclick="aLoadDeliveryUnits(this);" data-dashboardmenu='a_infrastructure'>Delivery Units</span>-->
    </div>
    <div id="a_editor" class="tab-pane fade">
		<span onclick="eLoadPublications(document.getElementById('aDashboardLinkPublications'));" id='aDashboardLinkPublications' 
			data-dashboardmenu='a_editor'>{!!$htexts['page_publications']->title!!}</span>&nbsp;|
		<span onclick="aLoadPersons(this);" id='aDashboardLinkPersons' data-dashboardmenu='a_editor'>{!!$htexts['str_persons']->title!!}</span>&nbsp;|
		<span onclick="aLoadSlides(this);" id='aDashboardLinkSlides' data-dashboardmenu='a_editor'>{!!$htexts['str_slides']->title!!}</span>&nbsp;|
		<span onclick="aLoadTexts(this);" id='aDashboardLinkTexts' data-dashboardmenu='a_editor'>{!!$htexts['str_texts']->title!!}</span>&nbsp;|
		<span onclick="aLoadLinks(this);" id='aDashboardLinkLinks' data-dashboardmenu='a_editor'>{!!$htexts['str_links']->title!!}</span>
    </div>
  </div>
</div>

<script>
	//window.addEventListener('load', aLoadCrops(document.getElementById("aDashboardLinkCrops")) );	
</script>
