
function fDashboardActionUpdateCultivationAssignment(ca, el) {
	let windowTitle = `${_myStrCultivation} <mark>${ca.title}</mark> | ${_myStrUpdating}`;

	let values = { is_accepted:ca.is_accepted, 
		amount_prognosed:ca.amount_prognosed, amount_actual:ca.amount_actual, 
		start_prognosed:ca.start_prognosed, start_actual:ca.start_actual, 
		finish_prognosed:ca.finish_prognosed, finish_actual:ca.finish_actual, 
		work_time:ca.work_time, id:ca.id };
	
	let keyProperties = { id: { hidden:true }, is_accepted:{}, 
		amount_prognosed:{required:false}, amount_actual:{required:false},
		start_prognosed:{type:'date', required:false}, finish_prognosed:{type:'date', required:false}, 
		start_actual:{type:'date', required:false}, finish_actual:{type:'date', required:false},
		work_time:{type:'number', required:false} };
	let rightPaneHTML = `<div>${_myStrCultivation}<br/><mark>${ca.title}</mark><br/>` + 
		`${_aDashboardFieldTitles.amount_by_plan}: <a href='/workflow/by_ca_id/${ca.id}' target=_blank>${ca.amount_by_plan}</a></br>`+
		`${_aDashboardFieldTitles.start_by_plan}: <a href='/a_workflow/by_ca_id/${ca.id}' target=_blank>${ca.start_by_plan}</a></br>`+
		`${_aDashboardFieldTitles.finish_by_plan}: <a href='/a_workflow/by_ca_id/${ca.id}' target=_blank>${ca.finish_by_plan}</a></br>`+
		`${ca.descr}</div>`; 
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/f_cultivation_assignments_update' } );
}


function fDashboardActionDisplayOperations( ca, el ) {
	let cbDateStart = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.start_by_plan, data.start_prognosed, data.start_actual);
		el.innerHTML = r[0];
		el.title= r[1];
	};
	let cbDateFinish = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.finish_by_plan, data.finish_prognosed, data.finish_actual);
		el.innerHTML = r[0];
		el.title= r[1];
	};

	let parameters = { 
		dataTitle: `${_myStrCultivation} "<mark>${ca.title}</mark>" [${ca.start_by_plan} - ${ca.finish_by_plan}] => ${_myStrOperations}`,
		display: { id:{ width:'3' }, title: { width:'10%' }, descr: { width: '10%' },
			start_by_plan:{width:'18', cb:cbDateStart, title:aDashboardGetPlanProgActStartTitle() }, 
			finish_by_plan:{width:'18', cb:cbDateFinish, title:aDashboardGetPlanProgActFinishTitle() } }, 
		tableActions: [],
		rowActions: [ 
			{ title: 'Edit', cb: fDashboardActionUpdateOperation, glyphIcon:'glyphicon glyphicon-pencil' } 	
		] 
	};
	aRequestDashboardDataArray(`/a_operations/ca/${ca.id}`, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:fDashboardActionDisplayOperations, arg1:ca, arg2:el }
	);
	// let windowTitle = `The Operation List for <mark>${ca.title}</mark> (${ca.start_by_plan} - ${ca.finish_by_plan})`;
}


function fDashboardActionUpdateOperation( op, el ) {
	let windowTitle = `${_myStrOperation} <mark>${op.title}</mark> | ${_myStrUpdating}`;

	let values = { 
		id:op.id, title: op.title, descr: op.descr, 
		start_prognosed:op.start_prognosed, finish_prognosed:op.finish_prognosed, 
		start_actual:op.start_actual, finish_actual:op.finish_actual, 
		cultivation_assignment_id: op.cultivation_assignment_id };

	let keyProperties = { id: { hidden:true }, cultivation_assignment_id:{hidden:true}, 
		start_prognosed:{type:'date'}, finish_prognosed:{type:'date'}, start_actual:{type:'date'}, finish_actual:{type:'date'} };

	let rightPaneHTML = `${_myStrOperation}:<br/><b><mark>${op.title}</mark></b><br/>`+
		`${_aDashboardFieldTitles.start_by_plan}: ${op.start_by_plan}</br>${_aDashboardFieldTitles.finish_by_plan}: ${op.finish_by_plan}`+
		`<br/><br/>${_myStrCultivation}:<br/>`+
		`<b><mark>${op.ca_title}</mark></b></br>`+
		`${_aDashboardFieldTitles.start_by_plan}: ${op.ca_start_by_plan}</br>`+
		`${_aDashboardFieldTitles.finish_by_plan}: ${op.ca_finish_by_plan}`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/f_operations_update' } );
}


function fDashboardActionUpdateHarvestingAssignment(ha, el) {
	let windowTitle = `Edit Harvesting Assignment ${ha.title}`;

	let values = { is_accepted:ha.is_accepted, 
		amount_prognosed:ha.amount_prognosed, amount_actual:ha.amount_actual, 
		start_prognosed:ha.start_prognosed, start_actual:ha.start_actual, 
		finish_prognosed:ha.finish_prognosed, finish_actual:ha.finish_actual, work_time:ha.work_time, id:ha.id };
	
	let keyProperties = { id: { hidden:true }, is_accepted:{}, 
		amount_prognosed:{required:false}, amount_actual:{required:false}, work_time:{type:'number', required:false},
		start_prognosed:{type:'date', required:false}, finish_prognosed:{type:'date', required:false}, 
		start_actual:{type:'date', required:false}, finish_actual:{type:'date', required:false} };
	let rightPaneHTML = `<div>Edit Harvesting Assignment<br/><mark>${ha.title}</mark><br/>${ha.descr}</div>`;
	rightPaneHTML += `Amount by Plan: <a href='/workflow/by_ha_id/${ha.id}' target=_blank>${ha.amount_by_plan}</a></br>`;
	rightPaneHTML += `Start by Plan: <a href='/a_workflow/by_ha_id/${ha.id}' target=_blank>${ha.start_by_plan}</a></br>`;
	rightPaneHTML += `Finish by Plan: <a href='/a_workflow/by_ha_id/${ha.id}' target=_blank>${ha.finish_by_plan}</a></br>`;
	rightPaneHTML += `<div style='margin-top:12px; text-align:center;'>Cultivation Assignment Details<br/>`;
	rightPaneHTML += `<mark>${ha.ca_title}</mark><br/>${ha.ca_finish_prognosed}</div>`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/f_harvesting_assignments_update' } );
}
