<script>
	function uRequestSupervisorDetailsForFarmerDashboard(farm_id, el) {
		let url = `/f_supervisor/`;
		fetch(url).then(data=>data.json()).then( function(data) { 
			el.innerHTML = `Your supervisor: <b>${data[0].name}</b>, ${data[0].contacts}</a>, <b>${data[0].email}</b>`;
		}).catch( function() { el.innerHTML=`Failed to load your supervisor's details. Please reload the page...` } );
	}
</script>

<h3>Farmer's</h3>

<div class="container" style='width:100%; box-sizing:border-box; margin:0px; padding:0px;'>
	<div class='row'>
		<div class='col-sm-3'>
			<div class='well' style='padding:12px;'>
				<a href='/workflow/by_farm_id/{{Auth::user()->farm_admin}}' target=_blank>{!!$htexts['page_workflow']->title!!}</a><br/>
			</div>
		</div>
		<div class='col-sm-9'>
			<div class='well' style='padding:12px;'>
				<span id='fDashboardSupervierDetails'></span>
				<script>
					uRequestSupervisorDetailsForFarmerDashboard( {{Auth::user()->farm_admin}}, 
						document.getElementById('fDashboardSupervierDetails') );
				</script>
			</div>
		</div>
	</div>
</div>

<div class="container" style='width:100%; box-sizing:border-box; margin:0px; padding:0px;'>
  <ul class="nav nav-tabs" style='width:100%;'>
    <li class="active">
		<a data-toggle="tab" href="#f_cultivationassignments" data-defaultsubmenuid="fDashboardLinkCultivationAssignments">
			{!!$htexts['str_cultivations']->title!!}<!--Cultivation Assignments--></a>
	</li>
    <li>
		<a data-toggle="tab" href="#f_harvestingassignments" data-defaultsubmenuid="fDashboardLinkHarvestingAssignments">
			{!!$htexts['str_harvestings']->title!!}<!--Harvesting Assignments--></a>
	</li>
  </ul>

	<div class="tab-content" style='width:100%;' id="fDashboardLinks">
		<div id="f_cultivationassignments" class="tab-pane fade in active">
			<span id='fDashboardLinkCultivationAssignments' data-dashboardmenu='f_cultivationassignments' 
				onclick="fLoadCultivationAssignments(document.getElementById('fDashboardLinkCultivationAssignments'),'all');">
				{!!$htexts['str_cultivations']->title!!}
			</span>
			<!--<span onclick="fLoadCultivationAssignments(this,'finished');" data-dashboardmenu='f_cultivationassignments'>Done
			</span>-->
		</div>
		<div id="f_harvestingassignments" class="tab-pane fade">
			<span id='fDashboardLinkHarvestingAssignments' data-dashboardmenu='f_harvestingassignments'  
				onclick="fLoadHarvestingAssignments(document.getElementById('fDashboardLinkHarvestingAssignments'),'all');" >
					{!!$htexts['str_harvestings']->title!!}</span>
			<!--<span onclick="fLoadHarvestingAssignments(this,'finished');" data-dashboardmenu='f_harvestingassignments'>Done</span>-->
		</div>
	</div>
</div>
