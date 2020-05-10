
function fLoadCultivationAssignments(el=null, request=null) {
	let cbTitle = function(data,el) {
		let text = `${data.title} / ${data.descr}`; 
		el.innerHTML=`${text}`;
		el.title=`${text}`;
	};
	let cbAmount = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.amount_by_plan, data.amount_prognosed, data.amount_actual);
		el.innerHTML = `<a href='/workflow/by_ca_id/${data.id}' target=_blank>${r[0]} / ${data.reserved}</a>`;
		el.innerTitle = `${r[1]} / ${data.reserved}`;
	};
	let cbDateStart = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.start_by_plan, data.start_prognosed, data.start_actual);
		el.innerHTML = `<a href='/a_workflow/by_ca_id/${data.id}' target=_blank>${r[0]}</a>`;
		el.title= r[1];
	};
	let cbDateFinish = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.finish_by_plan, data.finish_prognosed, data.finish_actual);
		el.innerHTML = `<a href='/a_workflow/by_ca_id/${data.id}' target=_blank>${r[0]}</a>`;
		el.title= r[1];
	};
	let parameters = { 
		display: { 
			is_finished: { width:'2', 
				cb: function(d,e) { e.innerHTML = aDashboardHelperFormatDoneOrNotDoneCell(d.is_finished==1); } },
			is_accepted: { width:'2', 
				cb: function(d,e) { e.innerHTML = aDashboardHelperFormatDoneOrNotDoneCell(d.is_accepted==1); } },
			title: { width:'24', cb:cbTitle }, 
			amount_by_plan: { width:'20', cb:cbAmount, title:aDashboardGetPlanProgActResAmountTitle() }, 
			start_by_plan: { width:'24', cb:cbDateStart, title:aDashboardGetPlanProgActStartTitle() }, 
			finish_by_plan: { width:'24', cb:cbDateFinish, title:aDashboardGetPlanProgActFinishTitle() } 
		},
		tableActions: [],
		rowActions: [ 
			{ title: 'Edit Cultivation Assignment', cb: fDashboardActionUpdateCultivationAssignment, glyphIcon:'glyphicon glyphicon-pencil' }, 	
			{ title: 'Operations', cb: fDashboardActionDisplayOperations, glyphIcon:'glyphicon glyphicon-tasks' }	
		] 
	};
	if( el === null ) {
		el = document.getElementById("fDashboardLinkCultivationAssignments")
	}
	if( request === null ) {
		request = 'pending';		
	}

	aRequestDashboardDataArray('/f_cultivation_assignments/'+request, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);
		},
		{cb:fLoadCultivationAssignments, arg1:el, arg2:request }
	);
}


function fLoadHarvestingAssignments(el=null, request=null) {
	if( el === null ) {
		el = document.getElementById("fDashboardLinkHarvestingAssignments")
	}

	let cbTitle = function(data,el) {
		let text = `${data.title} / ${data.descr}`; 
		el.innerHTML=`${text}`;
		el.title=`${text}`;
	};
	let cbAmount = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.amount_by_plan, data.amount_prognosed, data.amount_actual);
		el.innerHTML = `<a href='/workflow/by_ha_id/${data.id}' target=_blank>${r[0]}</a>`;
		el.innerTitle = `${r[1]} / ${data.reserved}`;
	};
	let cbDateStart = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.start_by_plan, data.start_prognosed, data.start_actual);
		el.innerHTML = `<a href='/a_workflow/by_ha_id/${data.id}' target=_blank>${r[0]}</a>`;
		el.title= r[1];
	};
	let cbDateFinish = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.finish_by_plan, data.finish_prognosed, data.finish_actual);
		el.innerHTML = `<a href='/a_workflow/by_ha_id/${data.id}' target=_blank>${r[0]}</a>`;
		el.title= r[1];
	};

	if( request === null ) {
		request = 'pending';		
	}

	let parameters = { 
		display: { 
			is_finished: { width:'2', 
				cb: function(d,e) { e.innerHTML = aDashboardHelperFormatDoneOrNotDoneCell(d.is_finished==1); } },
			is_accepted: { width:'2', 
				cb: function(d,e) { e.innerHTML = aDashboardHelperFormatDoneOrNotDoneCell(d.is_accepted==1); } },
			title: { width:'24', cb:cbTitle }, 
			amount_by_plan: { width:'20', cb:cbAmount, title:aDashboardGetPlanProgActAmountTitle() }, 
			start_by_plan: { width:'24', cb:cbDateStart, title:aDashboardGetPlanProgActStartTitle() }, 
			finish_by_plan: { width:'24', cb:cbDateFinish, title:aDashboardGetPlanProgActFinishTitle() } 
		},
		tableActions:[], 
		rowActions:[
			{ title: 'Edit Harvesting Assignment', cb: fDashboardActionUpdateHarvestingAssignment, glyphIcon:'glyphicon glyphicon-pencil' }, 	
		] 
	};
	aRequestDashboardDataArray('/f_harvesting_assignments/'+request, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);
		},
		{cb:fLoadHarvestingAssignments, arg1:el, arg2:request }
	);
}
