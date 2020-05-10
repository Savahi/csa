<h3>Delivery's</h3>
<div class="" style='margin-top:0px; margin-bottom:0px; padding-top:0px; padding-bottom:0px;'>
  <ul class="nav nav-tabs" style='width:100%;'>
    <li class="active">
		<a data-toggle="tab" href="#d_supplies" data-defaultsubmenuid="dDashboardLinkSupplies">	
			{!!$htexts['page_supplies']->title!!}</a>
	</li>
  </ul>
	<div class="tab-content" style='width:100%;' id="dDashboardLinks">
		<div id="d_supplies" class="tab-pane fade in active">
			<span id='dDashboardLinkSupplies' data-dashboardmenu="d_supplies"
				onclick="dLoadSupplies(document.getElementById('dDashboardLinkSupplies'));">{!!$htexts['page_supplies']->title!!}</span>
		</div>
	</div>
</div>

<script>
	; // window.addEventListener('load', dLoadSupplies(document.getElementById('dDashboardLinkSupplies')) );	
</script>