
function aDashboardActionUpdateUser( user, el ) {
	let windowTitle = `${_aDashboardFieldTitles.name} <mark>${user.name}</mark> | ${_myStrUpdating}`;

	let values = { id: user.id, email:user.email, contacts:user.contacts };
	if( 'balance' in user )
		values.balance = user.balance; 
	if( 'deposit' in user ) 
		values.deposit = user.deposit; 
	if( 'deposit_comment' in user ) 
		values.deposit_comment = user.deposit_comment; 

	if( 'delivery_point_id' in user ) { values.delivery_point_id = user.delivery_point_id; }
	if( 'delivery_point_admin' in user ) { values.delivery_point_admin = user.delivery_point_admin; }
	if( 'farm_admin' in user ) { values.farm_admin = user.farm_admin; }
	if( 'delivery_unit_admin' in user ) { values.delivery_unit_admin = user.delivery_unit_admin; }
	
	let kprops = { id: { hidden:true }, balance: {hidden:true} };
	if( 'deposit_comment' in user ) {
		kprops.deposit_comment = { title:`${_myStrDepositComment}` };
	}

	let rpane = aDashboardHelperFormatUserDetails(user);

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, { rightPaneHTML:rpane, keyProperties:kprops, saveURL:'/a_users_update' } );
}


function aDashboardActionDeleteUser( user, el ) {
	let windowTitle = `${_aDashboardFieldTitles.name} <mark>${user.name}</mark> | ${_myStrDeleting}`;

	if( user.admin_privilegies > 0 || user.delivery_point_id > 0 || user.delivery_point_admin > 0 || 
		user.farm_admin > 0 || user.delivery_unit_admin > 0 || user.balance != 0 || user.deposit != 0 ) {
		alert('You can not delete this user since they has a role in the system!');
		return;		
	}

	let values = { id:user.id, admin_privileges:user.admin_privileges, delivery_point_id:user.delivery_point_id, 
		delivery_point_admin: user.delivery_point_admin, farm_admin: user.farm_admin, delivery_unit_admin:user.delivery_unit_admin };
	
	let kprops = { id: { hidden:true }, admin_privileges: { hidden:true }, delivery_point_id: { hidden:true }, 
		delivery_point_admin: { hidden:true }, farm_admin: { hidden:true }, delivery_unit_admin: { hidden:true } };
	let pane = aDashboardHelperFormatUserDetails(user) + `<h1>${_myStrContinue}?</h1>`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ leftPaneHTML:pane, rightPaneHTML:null, keyProperties:kprops, saveURL:'/a_users_delete' } );
}


function aDashboardActionRefillUser(user, el) {
	let windowTitle = `${_aDashboardFieldTitles.name} <mark>${user.name}</mark> | ${_myStrRefilling}`;

	let dateNow = new Date();
	let datetime = calendarDateTimeToString(dateNow, false);

	let values = { id: user.id, title:'A Refill', amount:null, made_at:datetime };
	let keyProperties = { id: { hidden:true }, amount: { type:'number', required:true, title:'<b>+</b>&#36;' }, 
		title: {type:'textarea', required:true }, 
		made_at: { type:'datetime', required:true, title:`${_aDashboardFieldTitles.made_at}` } };

	let rightPaneHTML = aDashboardHelperFormatUserDetails(user);
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_users_refill' } );	
}

/* Not used!!!!
function aDashboardActionDebetUser(user, el) {
	let windowTitle = `${_aDashboardFieldTitles.name} <mark>${user.name}</mark> | ${_myStrDebeting}`;

	let dateNow = new Date();
	let datetime = calendarDateTimeToString(dateNow, false);

	let values = { id: user.id, title:'A Debeting', amount:null, made_at:datetime };
	let keyProperties = { id: { hidden:true }, amount: { type:'number', required:true, title:'<b>-</b>&#36;' }, 
		title: {type:'textarea', required:true }, 
		made_at: { type:'datetime', required:true, title:`${_aDashboardFieldTitles.made_at}` } };

	let rightPaneHTML = aDashboardHelperFormatUserDetails(user);
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_users_debet' } );	
}
*/

function aDashboardActionDeleteRefill(data) {
	if( confirm(`${_myMsgRevokeRefill}?`) ) {
		let params = { 'keys': ['id'], 'inputs': [ { value:data.id } ], saveURL:'/a_users_delete_refill' };
		aDefaultDashboardDataEditWindowSaveFunction( params );
	}
}

function aDashboardActionUpdateRefill(refill, el) {
	let windowTitle = `${_myStrRefilling} | ${_myStrUpdating}`;

	let values = { id: refill.id, title:refill.title, amount:refill.amount, made_at:refill.made_at };
	let keyProperties = { id: { hidden:true }, amount: { type:'number', required:true, title:'&#177;&#36;' }, 
		title: {type:'textarea', required:true }, 
		made_at: { type:'datetime', required:true, title:`${_aDashboardFieldTitles.made_at}` } };

	let rightPaneHTML = `${_myStrRefilling}<br/>` + 
		`<span class="glyphicon glyphicon-user"></span>: <mark>${refill.user_name}</mark><br/>` + 
		`<span class="glyphicon glyphicon-envelope"></span>: <mark>${refill.user_email}</mark>`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_users_update_refill' } );	
}


function aDashboardActionDisplayUserRefills( user, el ) {
	let parameters = { 
		dataTitle: `User: ${user.name} (${user.email}) => Refills`, display: { id:{ width:'3' }, made_at:{width:'14'}, 
		amount: { width: '6', title:'<span class="glyphicon glyphicon-usd"></span>' }, title: { width:'20' } }, 
		rowActions: [
			{ title: `${_myStrUpdate}`, cb: aDashboardActionUpdateRefill, glyphIcon:'glyphicon glyphicon-pencil'}, 	
			{ title: 'Revoke refill', cb: aDashboardActionDeleteRefill, glyphIcon:'glyphicon glyphicon-remove'}, 	
		], 
		tableActions: [],
	};
	aRequestDashboardDataArray(`/a_users/refills/${user.id}`, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aDashboardActionDisplayUserRefills, arg1:user, arg2:el }
	);
}


function aDashboardActionDeleteDebeting( debeting ) {
	if( confirm('Are you sure to revoke this debeting?') ) {
		let params = { 'keys': ['id'], 'inputs': [ { value:debeting.id } ], saveURL:'/a_users_delete_debeting' };
		aDefaultDashboardDataEditWindowSaveFunction( params );
	}
}


function aDashboardActionDisplayUserDebetings( user, el ) {
	let parameters = { 
		dataTitle: `User: ${user.name} (${user.email}) => Debetings`,
		display: { 
			id:{ width:'3' }, made_at:{width:'10'}, amount: { width: '6', title:'<span class="glyphicon glyphicon-usd"></span>' }, 
			supply_title: { width:'14', title:'Supply' },
			is_delivered: { width:'2', title:'<span class="glyphicon glyphicon-shopping-cart"></span>', tooltip:'Delivered?', cb:aDashboardIsDeliveredCB }  
		}, 
		tableActions: [],
		rowActions: [
			{ title: 'Revoke debeting', cb: aDashboardActionDeleteDebeting, glyphIcon:'glyphicon glyphicon-remove'}, 	
		] 
	};
	aRequestDashboardDataArray(`/a_users/debetings/${user.id}`, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aDashboardActionDisplayUserDebetings, arg1:user, arg2:el }
	);
}

// **** CROP SECTION

function aDashboardActionCreateCrop( crop, el ) {
	let windowTitle = `${_myPageCrops}. <b>${_myStrCreate}</b>`;
	let values = { title: '...', descr: '...', icon: null };
	let keyProperties = { title:{required:true}, descr:{required:true} };
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, { rightPaneHTML:'', keyProperties:keyProperties, saveURL:'/a_crops_new' } );
}

function aDashboardActionFormatCropDetails( crop ) {
	return `<div style='font-size:120%; text-align:left;'>${_myStrCrop}: <mark>${crop.title}</mark><br/>(id: ${crop.id})<br/>${crop.descr}.</div>`;
}

function aDashboardActionUpdateCrop( crop, el ) {
	let windowTitle = `<mark>${crop.title}</mark>. <b>${_myStrUpdating}</b>`;
	let values = { title: crop.title, descr: crop.descr, icon: crop.icon, id: crop.id };
	let keyProperties = { id: { hidden:true }, title:{required:true}, descr:{required:true} };
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:aDashboardActionFormatCropDetails(crop), keyProperties:keyProperties, saveURL:'/a_crops_update' } );
}

function aDashboardActionDeleteCrop( crop, el ) {
	aDisplayDashboardDataArrayEditWindow( `<mark>${crop.title}</mark>`, null, { id:crop.id }, 
		{ leftPaneHTML:`<h1>${_myStrDelete}?</h1>`, rightPaneHTML:aDashboardActionFormatCropDetails(crop), 
		keyProperties:{ id:{hidden:true} }, saveURL:'/a_crop_delete' } );
}


// **** CULTIVATION ASSIGNMENT SECTION
function aDashboardActionCAKeyProperties() {
	return { crop_id: { hidden:true }, start_by_plan:{type:'date' }, finish_by_plan:{type:'date'}, 
		farm_id:{required:true}, amount_by_plan:{type:'number', min:1, required:true}, square:{type:'number', min:1,required:false} };
}

function aDashboardActionCreateCultivationAssignment( crop, el ) {
	let windowTitle = `${_myStrCultivation} (<mark>${crop.title}</mark>) | ${_myStrCreating}`;

	let date = new Date();
	let start_by_plan = calendarDateToString( date );
	date.setDate( date.getDate()+60);
	let finish_by_plan = calendarDateToString( date );
	let values = { 
		title: crop.title, descr: '...', 
		farm_id: '', amount_by_plan:'', start_by_plan:start_by_plan, finish_by_plan:finish_by_plan, crop_id: crop.id, square:null };
	
	let keyProperties = aDashboardActionCAKeyProperties();
	let rightPaneHTML = aDashboardActionFormatCropDetails(crop);

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_cultivation_assignments_new' } );
}


function aDashboardActionFormatCultivationAssignmentDetails( ca ) {
	return `${_myStrCultivation}:<br/><mark>${ca.title}</mark><br/>`+
		`${_myStrAccepted}: ${aDashboardHelperFormatDoneOrNotDoneCell(ca.is_accepted)}<br/>`+
		`${_myStrFinished}: ${aDashboardHelperFormatDoneOrNotDoneCell(ca.is_finished)}<br/>`+
		`${_aDashboardFieldTitles.start_by_plan}: ${ca.start_by_plan}</br>`+
		`${_aDashboardFieldTitles.finish_by_plan}: ${ca.finish_by_plan}</br>${_aDashboardFieldTitles.amount_by_plan}: ${ca.amount_by_plan}</br>`+
		`${_aDashboardFieldTitles.square}: ${ca.square}<br/><br/>`;
}


function aDashboardActionUpdateCultivationAssignment( ca, el ) {
	let windowTitle = `${_myStrCultivation} <mark>${ca.title}</mark> | ${_myStrUpdating}`;

	let values = { id:ca.id, title:ca.title, descr:ca.descr, crop_id:ca.crop_id, farm_id:ca.farm_id, 
		amount_by_plan:ca.amount_by_plan, start_by_plan:ca.start_by_plan, finish_by_plan:ca.finish_by_plan, square:ca.square };

	let keyProperties = aDashboardActionCAKeyProperties();
	keyProperties.id = { hidden:true };
	let rightPaneHTML = document.createElement('div');
	let rightPaneTitle = aDashboardActionFormatCultivationAssignmentDetails(ca);
	aRequestDataForDashboardEditWindow( `/a_harvesting_assignments/ca/${ca.id}`, `${_myStrHarvesting}`, 
		function(ha) {
			aDashboardActionCreateHarvestingListDiv(ha, rightPaneTitle, rightPaneHTML);
		});

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_cultivation_assignments_update' } );
}

function aDashboardActionDeleteCultivationAssignment( ca, el ) {
	let windowTitle = `${_myStrCultivation} <mark>${ca.title}</mark> | ${_myStrDeleting}` ;
	let props = { id: { hidden:true } };
	let details = aDashboardActionFormatCultivationAssignmentDetails(ca);
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, { id:ca.id }, 
		{ rightPaneHTML:details, leftPaneHTML:`<h1>${_myStrDelete}?</h1>`, keyProperties:props, saveURL:'/a_cultivation_assignments_delete' } );
}

// **** OPERATIONS SECTION

function aDashboardActionDisplayOperations( ca, el ) {
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
		dataTitle: `${_myStrCultivation} "${ca.title}" [${ca.start_by_plan} - ${ca.finish_by_plan}] => ${_myStrOperations}`,
		display: { id:{ width:'3' }, title: { width:'10%' }, descr: { width: '10%' },
			start_by_plan:{width:'18', cb:cbDateStart, title:aDashboardGetPlanProgActStartTitle()}, 
			finish_by_plan:{width:'18', cb:cbDateFinish, title:aDashboardGetPlanProgActFinishTitle()} }, 
		tableActions: [			
			{ title: 'Create New Operation', cb: aDashboardActionCreateOperation, 
				cbargs:{ ca_id:ca.id, ca_title:ca.title, ca_start_by_plan:ca.start_by_plan, ca_finish_by_plan:ca.finish_by_plan }, 
				glyphIcon:'glyphicon glyphicon-plus' } 
		],
		rowActions: [ 
			{ title: 'Delete', cb: aDashboardActionDeleteOperation, glyphIcon:'glyphicon glyphicon-remove' }, 	
			{ title: 'Edit', cb: aDashboardActionUpdateOperation, glyphIcon:'glyphicon glyphicon-pencil' } 	
		] 
	};
	aRequestDashboardDataArray(`/a_operations/ca/${ca.id}`, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aDashboardActionDisplayOperations, arg1:ca, arg2:el }
	);
}

function aDashboardActionOperationKeyProperties() {
	return { title:{required:true}, descr:{required:true}, cultivation_assignment_id: { hidden:true }, 
		start_by_plan:{type:'date'}, finish_by_plan:{type:'date'} };
}

function aDashboardActionCreateOperation( ops, params ) {
	let windowTitle = `${_myStrOperation} | ${_myStrCreating}`;
	let values = { 
		title: '', descr:'', start_by_plan:params.ca_start_by_plan, finish_by_plan:params.ca_finish_by_plan,
		cultivation_assignment_id: params.ca_id };
	let keyProperties = aDashboardActionOperationKeyProperties(); // { cultivation_assignment_id: { hidden:true } };
	let rightPaneHTML = `${_myStrCultivation}:</br><mark>${params.ca_title}</mark></br>`+
		`${_aDashboardFieldTitles.start_by_plan}: ${params.ca_start_by_plan}</br>${_aDashboardFieldTitles.finish_by_plan}: ${params.ca_finish_by_plan}`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_operations_new' } );
} 

function aDashboardActionUpdateOperation( op, el ) {
	let windowTitle = `${_myStrOperation} <mark>${op.title}</mark> | ${_myStrUpdating}`;

	let values = { 
		id:op.id, title: op.title, descr: op.descr, 
		start_by_plan:op.start_by_plan, finish_by_plan:op.finish_by_plan, 
		cultivation_assignment_id: op.cultivation_assignment_id };

	let keyProperties = aDashboardActionOperationKeyProperties();
	keyProperties.id = {hidden:true}; // , cultivation_assignment_id:{hidden:true} };

	let rightPaneHTML = `${_myStrOperation}:<br/><mark>${op.title}</mark><br/>`+
		`${_aDashboardFieldTitles.start_by_plan}: ${op.start_by_plan}<br/>`+
		`${_aDashboardFieldTitles.finish_by_plan}: ${op.finish_by_plan}<br/><br/>`+
		`${_myStrCultivation}:</br><mark>${op.ca_title}</mark></br>`+
		`${_aDashboardFieldTitles.start_by_plan}: ${op.ca_start_by_plan}</br>${_aDashboardFieldTitles.finish_by_plan}: ${op.ca_finish_by_plan}`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_operations_update' } );
}


function aDashboardActionDeleteOperation( op, el ) {
	let windowTitle = 'Deleting Operation <mark>' + op.title + '</mark>';

	let values = { id:op.id };
	let keyProperties = { id: { hidden:true } };
	let rightPaneHTML = `<div align=center>Deleting Operation <b><mark>${op.title}</mark></b></div>`;
	rightPaneHTML += `<h1>Are You Sure Deleting This Operation?</h1><br/><i>Cultivation Assignment Details</i><br/>`;
	rightPaneHTML += `Title: <b><mark>${op.ca_title}</mark></b></br>`;
	rightPaneHTML += `Start by Plan: ${op.ca_start_by_plan}</br>Finish by Plan: ${op.ca_finish_by_plan}`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ leftPaneHTML:rightPaneHTML, rightPaneHTML:null, keyProperties:keyProperties, saveURL:'/a_operations_delete' } );
}


function aDashboardActionShowCultivationAssignments(crop, el) {
	let windowTitle = `<mark>${crop.title}</mark> | ${_myStrCultivations}`;
    myPopupDivShow( windowTitle, null, { mode:1, size:'h' } );
	aRequestDataForDashboardEditWindow( '/a_cultivation_assignments/cr/'+crop.id, `${_myStrCultivations}`, 
		function(r) {
			if( r === null ) {
				return;
			}
			let body = myPopupDivBody();
			body.innerHTML = '';
			for( let i = 0 ; i < r.length ; i++ ) {
				body.innerHTML += `<div><span class="glyphicon glyphicon-calendar"></span>`+ 
					`<a href='/a_workflow/by_ca_id/${r[i].id}' target=_blank>${r[i].finish_prognosed}</a>. `+ 
					`${_myStrFarm}: <a href='/workflow/by_farm_id/${r[i].farm_id}' target=_blank>${r[i].fa_title}</a></div>`;
			}					
		});
}

function aDashboardActionHAKeyProperties() {
	return keyProperties = { title:{required:true}, descr:{required:true},
		amount_by_plan: { type:'number', required:true }, start_by_plan: { type:'datetime', required:true},
		finish_by_plan: { type:'datetime', required:true}, supply_id: {required:true},
		cultivation_assignment_id: { hidden:true }, crop_id: { hidden:true }, farm_id: { hidden:true } };
}


function aDashboardActionCreateHarvestingAssignment( ca, el ) {
	let windowTitle = `${_myStrHarvesting} | ${_myStrCreating}`;

	let start_by_plan_date=null;
	if( ca.finish_actual !== null ) {
		start_by_plan_date = calendarStringToDate(ca.finish_actual);
	} else if( ca.finish_prognosed !== null > 0 ) {
		start_by_plan_date = calendarStringToDate(ca.finish_prognosed);
	} else if( ca.finish_by_plan !== null ) {
		start_by_plan_date = calendarStringToDate(ca.finish_by_plan);
	}
	if( start_by_plan_date === null ) {
		start_by_plan_date = new Date();
	}
	start_by_plan_date = calendarAddDays( start_by_plan_date, 1 ); // Adding a day
	let start_by_plan = calendarDateTimeToString( start_by_plan_date );
	let finish_by_plan_date = calendarAddDays( start_by_plan_date, 1 );
	let finish_by_plan = calendarDateTimeToString( start_by_plan_date );
	if( ca.reserved === null ) {
		ca.reserved = 0;
	}
	let amount = ca.amount_prognosed;
	let amountAvailable = amount - ca.reserved;
	let values = { title: ca.title, descr: ca.title, 
		amount_by_plan:amountAvailable, start_by_plan:start_by_plan, finish_by_plan:finish_by_plan, supply_id:-1,
		cultivation_assignment_id: ca.id, crop_id:ca.crop_id, farm_id:ca.farm_id };

	let keyProperties = aDashboardActionHAKeyProperties();

	let rightPaneHTML = `<mark>${ca.title}</mark><br/>`+
		`${_myStrAmount} (${_myStrOverall}): ${amount} ${_myConstWeightUnit}<br/>`+
		`${_myStrReserved}: <mark>${ca.reserved}</mark> ${_myConstWeightUnit}<br/>`+
		`${_myStrAvailable}: <mark>${amountAvailable}</mark><br/>`;
	if( !(amountAvailable > 0) ) {
		rightPaneHTML += `<h1>0 ${_myConstWeightUnit} ${_myStrAvailable}</h1>`;
	}
	rightPaneHTML += '<br/>' + aDashboardActionFormatCultivationAssignmentDetails( ca );

	let disableSave = (amountAvailable > 0) ? false : true;	
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_harvesting_assignments_new', disableSave:disableSave } );
}


function aDashboardActionFormatHarvestingAssignmentDetails( ha ) {
	return `${_myStrHarvesting}<br/><b><mark>${ha.title}</mark></b><br/>` + 
		`${_myStrAccepted}: ${aDashboardHelperFormatDoneOrNotDoneCell(ha.is_accepted)}<br/>`+
		`${_myStrFinished}: ${aDashboardHelperFormatDoneOrNotDoneCell(ha.is_finished)}<br/>`+
		`${_aDashboardFieldTitles.amount_by_plan}: ${ha.amount_by_plan}<br/>`+
		`${_myStrStart} (${_myStrPlan}): ${ha.start_by_plan}<br/>`+
		`${_myStrFinish} (${_myStrPlan}): ${ha.finish_by_plan}<br/>`;
}


function aDashboardActionUpdateHarvestingAssignment( ha, el ) {
	let windowTitle = `${_myStrHarvesting} <mark>${ha.title}</mark> | ${_myStrUpdating}`;

	let values = { id:ha.id, title: ha.title, descr: ha.descr, 
		amount_by_plan:ha.amount_by_plan, start_by_plan:ha.start_by_plan, finish_by_plan:ha.finish_by_plan, supply_id:ha.supply_id,
		cultivation_assignment_id: ha.cultivation_assignment_id, crop_id:ha.crop_id, farm_id:ha.farm_id };

    let available = ha.ca_amount_prognosed - (ha.reserved - ha.amount_prognosed);

	let kprops = aDashboardActionHAKeyProperties();
	kprops.id = { hidden:true };
	kprops.amount_by_plan.max = available;

	let rhtml = aDashboardActionFormatHarvestingAssignmentDetails(ha) + 
		`${_myStrAvailable}: <mark><b>${available}</b></mark> ${_myConstWeightUnit}`+ 
		`<br/><br/><span class="glyphicon glyphicon-link" style="font-size:140%;"></span><br/><br/>` +
		aDashboardActionFormatCultivationAssignmentDetails( { id:ha.ca_id, title:ha.ca_title, 
			start_by_plan:ha.ca_start_by_plan, finish_by_plan:ha.ca_finish_by_plan, amount_by_plan:ha.ca_amount_by_plan, square:ha.ca_square } );

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rhtml, keyProperties:kprops, saveURL:'/a_harvesting_assignments_update' } );
}


function aDashboardActionDeleteHarvestingAssignment( ha, el ) {
	let windowTitle = `${_myStrHarvesting} <mark>${ha.title}</mark> | ${_myStrDeleting}`;

	let values = { id:ha.id };
	let kprops = { id: { hidden:true } };
	let rightPane = document.createElement('div');
	aRequestDataForDashboardEditWindow( `/a_cultivation_assignments/${ha.cultivation_assignment_id}`, 'Requesting Cultivation Details', 
		function(ca) {
			let title = aDashboardActionFormatHarvestingAssignmentDetails(ha);
			aDashboardActionCreateCultivationDetailsDiv(ca, title, rightPane);
		});

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPane, leftPaneHTML:`<h1>${_myStrDelete}?</h1>`, keyProperties:kprops, saveURL:'/a_harvesting_assignments_delete' } );
}


// List of harvesting assignments attached to a supply...
function aDashboardActionCreateHarvestingListDiv(ha, titleHTML=null, containerDiv=null) {
	let container = ( containerDiv === null ) ? document.createElement('div') : containerDiv;
	if( titleHTML !== null ) {
		let title = document.createElement('div');
		title.innerHTML = titleHTML;
		container.appendChild(title);
	}
	if( ha.length == 0 ) {
		return container;
	}
	let hTitle = document.createElement('div');
	hTitle.innerHTML = `${_myStrHarvesting}:`;
	container.appendChild(hTitle);

	let table = document.createElement('table');
	for( let i = 0 ; i < ha.length ; i++ ) {
		let tr = document.createElement('tr');
		table.appendChild(tr);	

		tr.id = 'aDashboardHarvestingList'+i;
		let tdDel = document.createElement('td');
		tdDel.style.padding='4px 12px 4px 4px';
		tdDel.innerHTML = '<span class="glyphicon glyphicon-apple"></span>'; //'x';
		tr.appendChild(tdDel);	

		let tdTitle = document.createElement('td');
		tdTitle.style.padding='4px 12px 4px 4px';
		tdTitle.innerHTML = `[${ha[i].amount_prognosed}] ${ha[i].title}`;
		tr.appendChild(tdTitle);	
	}
	container.appendChild(table);
	return container;
}

// **** SUPPLY SECTION

function aDashboardActionSupplyKeyProperties() {
	return { deliver_from:{ hidden:true }, title: { required:true }, descr: { required:true }, 
		delivery_info:{ type:'textarea', required:true }, deliver_to:{ type:'datetime', required:true }, 
		price_per_user:{ type:'number', min:'1', required:true } };
}


function aDashboardActionFormatSupplyDetails(supply) {
	let cn = (supply.is_delivered) ? 'glyphicon glyphicon-check' : 'glyphicon glyphicon-unchecked';
	return `<mark><b>${supply.title}</b></mark><br/>${supply.deliver_to}<br/><span class='${cn}'></span>&nbsp;${_myStrDelivered}<br/>`;
}


function aDashboardActionCreateSupply( supply, el ) {	
	let windowTitle = `${_myStrSupply} | ${_myStrCreate}`;

	let date = new Date();
	date.setDate( date.getDate()+1 );
	date.setHours(11);
	date.setMinutes(0);
	let deliver_from = calendarDateTimeToString( date, false );
	date.setHours( date.getHours()+1 );
	let deliver_to = calendarDateTimeToString( date, false );
	let values = { title: '', descr: '', delivery_info: '', 
		deliver_from:deliver_from, deliver_to:deliver_to, price_per_user:'', icon: null };
	
	let kprops = aDashboardActionSupplyKeyProperties();
	let rpane = document.createElement('div');
	aRequestDataForDashboardEditWindow( '/a_cultivation_assignments_not_finished', '${_myStrCultivations}', 
		function(cas) {
			rpane.innerHTML = `<div style='font-size:120%; font-weight:bold;'>${_myStrCultivations}</div><br/>`;
			for( let i = 0 ; i < cas.length ; i++ ) {
				rpane.innerHTML += `&nbsp;&nbsp;&middot;&nbsp;${cas[i].finish_prognosed}: <i>${cas[i].title}</i><br/>`;
			}
		});
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, { rightPaneHTML:rpane, keyProperties:kprops, saveURL:'/a_supplies_new' } );
}


function aDashboardActionUpdateSupply( supply, el ) {	
	let windowTitle = `${_myStrSupply} <mark>${supply.title}</mark> | ${_myStrUpdating}`;

	let values = { id:supply.id, title: supply.title, descr: supply.descr, 
		deliver_to:supply.deliver_to, delivery_info: supply.delivery_info, price_per_user: supply.price_per_user, icon: supply.icon };
	
	let kprops = aDashboardActionSupplyKeyProperties();
	kprops.id = {hidden:true};
	
	let rightPaneTitle = aDashboardActionFormatSupplyDetails(supply);

	let rightPaneHTML = document.createElement('div');
	aRequestDataForDashboardEditWindow( `/a_harvesting_assignments/su/${supply.id}`, `${_myStrHarvestings}`, 
		function(ha) {
			aDashboardActionCreateHarvestingListDiv(ha, rightPaneTitle, rightPaneHTML);
		});

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, { rightPaneHTML:rightPaneHTML, keyProperties:kprops, saveURL:'/a_supplies_update' } );
}


function aDashboardActionDeleteSupply( supply, el ) {	
	let windowTitle = `${_myStrSupply} <mark>${supply.title}</mark> | ${_myStrDeleting}`;

	let values = { id:supply.id };
	let keyProperties = { id: { hidden:true } };
	
	let rightPaneTitle = aDashboardActionFormatSupplyDetails(supply);
	let rightPaneHTML = document.createElement('div');
	aRequestDataForDashboardEditWindow( `/a_harvesting_assignments/su/${supply.id}`, '${_myStrHarvesting}', 
		function(ha) {
			aDashboardActionCreateHarvestingListDiv(ha, rightPaneTitle, rightPaneHTML);
		});

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ leftPaneHTML:`<h1>${_myStrDelete}?</h1>`, rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_supplies_delete' } );
}


function aDashboardActionRevokeDebetingsForSupply( supply, el ) {	
	aDashboardActionDebetForSupplyHelper( supply, el, '-' );
}

function aDashboardActionDebetForSupply( supply, el ) {	
	aDashboardActionDebetForSupplyHelper( supply, el, '+' );
}

function aDashboardActionDebetForSupplyHelper( supply, el, op ) {	
    if( op === '+' ) {
		if( supply.price_per_user === null || supply.price_per_user == 0 ) {
			alert('The price is not set for the supply. Please, open supply settings and set the price first...');
			return;
		} 
	}
	
	let windowTitle = (op === '+') ? `${_myStrDebetingForSupply}...` : `${_myStrCancel} <i>${_myStrDebetingForSupply}</i>...`;

	let dateNow = new Date();
	let deliverTo = calendarStringToDate(supply.deliver_to);
	let timeDelta = 'unknown';
	if( deliverTo !== null ) {
		let diffHours = Math.floor( Math.abs(dateNow.getTime() - deliverTo.getTime()) / 3600000 );
		let diffDays = Math.floor( diffHours / 24);		
		timeDelta = `${diffHours} hours (${diffDays} days)`;
	}  

	let values = { id:supply.id, title:supply.title, price_per_user:supply.price_per_user };
	let keyProperties = { id: { hidden:true }, title: { hidden:true }, price_per_user: { hidden:true } };

	let rightPaneTitle = `${_myStrSupply}<br/><mark>${supply.title}</mark><br/>`;
	rightPaneTitle += `<span class="glyphicon glyphicon-calendar"></span><mark>${supply.deliver_to}</mark><br/>(${timeDelta})<br/>`;
	rightPaneTitle += `${_aDashboardFieldTitles.price_per_user}: <mark><b>${supply.price_per_user}</b></mark>`;
	let rightPaneHTML = document.createElement('div');
	aRequestDataForDashboardEditWindow( `/a_supplies/deb/${supply.id}/${supply.price_per_user}/`, 'Requesting Users debeted for supply...', 
		function(data) {
			if( 'error_message' in data ) {
				rightPaneHTML.innerHTML = data.error_message;
				document.getElementById('myPopupDivOk').disabled=true;
			} else {
				rightPaneHTML.innerHTML = rightPaneTitle + '<br/><br/>' +
					`${_myStrDebetingForSupply}: <b>${data[0].nd}</b> ${_myStrMade}/ <b>${data[0].nnd}</b> ${_myStrAvailable}`;
			}
		});

	let ppuText = `<mark><b>${supply.price_per_user}</b></mark> ${_myConstCurrencyUnit}&nbsp;/&nbsp;<span class="glyphicon glyphicon-user"></span>`;
	let leftPaneHTML = (op==='+') ? 
		`<div class="well" style='font-size:120%;'>${_myStrDebetingForSupply} (${ppuText}).<br/>${_myStrContinue}?` : 
		`<div class="well" style='font-size:120%;'><b>${_myStrCancel}</b> <i>${_myStrDebetingForSupply}</i> (${ppuText})?`;

	let saveURL = (op === '+') ? '/a_supplies_debet' : '/a_supplies_revoke_debetings';
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, leftPaneHTML:leftPaneHTML, keyProperties:keyProperties, saveURL:saveURL } );
}


function aDashboardCreateHarvestingsForSupplyEditPane(titleText, harvestingAssignments, values, splitFor=1) {

	let createLabelTexts = function(title, amount, max_amount, splitFor=1) {
		let t1, t2;
		if( title !== null ) {
	    	t1 = `<span style='font-size:16px;'>${title}</span>`;
		}
		let splitted = '';
		if( splitFor > 1 ) {
			splitted = `&nbsp;/&nbsp;<span style='font-size:15px;'>${(Math.round(amount*10.0 / splitFor)/10).toString()}</span>`;
		}			
		t2 = `<span style='font-size:18px; font-weight:bold;'>${amount}${splitted}</span><br/>`;
		t2 += `<span style='font-size:16px; color:#5f5f5f;'>[0&nbsp;&#8596;&nbsp;${max_amount}]</span>`;
		if( title !== null ) { 	
			return [t1,t2];
		}
		return t2;
	}
	                                                                                          	
	let inputs = [];
	
	let containerDiv = document.createElement('div');
	containerDiv.style.width = '100%';

	let titleDiv = document.createElement('div');
	titleDiv.style.width = '100%';
	titleDiv.style.textAlign = 'center';
	if( splitFor > 1 ) {
		titleText += ` (splitting for ${splitFor} parts)`;
	}
	titleDiv.innerHTML = titleText;

	let table = document.createElement('table');
	table.style.width='100%';
	table.cellSpacing='0';
	table.style.cellPadding='20';
	for( let index = 0 ; index < harvestingAssignments.length ; index++ ) {
		let hassign = harvestingAssignments[index];
		let amount = (hassign.amount_actual > 0) ? hassign.amount_actual : ((hassign.amount_prognosed > 0) ? hassign.amount_prognosed : hassign.amount_by_plan);

		let texts = createLabelTexts( hassign.title, amount, amount, splitFor );

		let tr = document.createElement('tr');	
		let td1 = document.createElement('td');	
		td1.style.width = '30%';
		td1.style.backgroundColor = (index % 2 == 0) ? '#ffffff' : '#e7e7e7';
		td1.style.textAlign = 'right';	
		td1.innerHTML = texts[0];
		td1.style.padding = '2px';
		tr.appendChild(td1);

		let tdm = document.createElement('td');	
		tdm.style.width = '20%';
		tdm.style.backgroundColor = (index % 2 == 0) ? '#ffffff' : '#e7e7e7';
		tdm.style.textAlign = 'center';	
		tdm.innerHTML = texts[1];
		tdm.style.padding= '2px';
		tr.appendChild(tdm);

		let td2 = document.createElement('td');	
		td2.style.width = '50%';
		td2.style.backgroundColor = (index % 2 == 0) ? '#ffffff' : '#e7e7e7';
		td2.style.padding = '2px';
		let input = document.createElement('input');
		input.type = 'range';
		input.style.width = '100%';
		input.dataset.key = 'hassign_' + hassign.id;
		input.min = 0;
		input.max = amount;
		input.value = amount;
		if( values !== null ) { 
			if( values[index] !== null ) {
				input.value = values[index];
			}
		}
		input.oninput = function() { tdm.innerHTML = createLabelTexts( null, input.value, amount, splitFor ); };
		td2.appendChild(input);
		tr.appendChild(td2);

		table.appendChild(tr);
		inputs.push( {index:index, element:input} );
	}
	containerDiv.appendChild( titleDiv );
	containerDiv.appendChild( table );

	return { container: containerDiv, inputs:inputs };
}


function aDashboardActionDisplayDPandSupplyDebetings( data, el ) {
	let parameters = { 
		dataTitle: `Supply: ${data.supply_title} (${data.supply_deliver_to}) => Delivery Point: ${data.delivery_point_title}`,
		display: { 
			id:{ width:'2' }, amount: { width: '6', title:'<span class="glyphicon glyphicon-usd"></span>' }, 
			user_name: { width:'14', title:'<span class="glyphicon glyphicon-user"></span>' },
			is_problem: { width: '2', cb:aDashboardIsProblemCB, title: '<span class="glyphicon glyphicon-info-sign"></span>', 
				tooltip:'You\'ll see a warning sign if a delivery problem happens' },
			is_delivered: { width:'2', title:'<span class="glyphicon glyphicon-shopping-cart"></span>', tooltip:'Delivered?', cb:aDashboardIsDeliveredCB }  
		}, 
		tableActions: [],
		rowActions: [
			{ title: 'Revoke debeting', cb: aDashboardActionDeleteDebeting, glyphIcon:'glyphicon glyphicon-remove'}, 	
		] 
	};
	aRequestDashboardDataArray(`/a_supplies/debetings/${data.supply_id}/${data.delivery_point_id}`, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aDashboardActionDisplayDPandSupplyDebetings, arg1:data, arg2:el }
	);
}


// **** DELIVERIES SECTION

function aDashboardActionDisplayDeliveries( supply, el ) {
	let fetchCB = function( dataObject, destElem ) { 
		let url = `/noauth_delivery_points_short/${dataObject.delivery_point_id}`;
		fetch(url).then(data=>data.json()).then( function(data) { 
			destElem.innerHTML=`<a href='/delivery_point/${dataObject.delivery_point_id}' target=_blank>${data[0].title}</a>`; 
		}).catch( function(e) { destElem.innerHTML='?'; } );
  	};

	let parameters = { 
		dataTitle: `<span style='font-weight:normal;'>${_myStrSupply} ${supply.title}, ${supply.deliver_to} =></span> ${_myPageDeliveryPoints}`,
		display: { 
			delivery_point_id: {width:'10', cb: fetchCB, tooltip:'Delivery Point' }, 
			cnt_deliveries: { width: '10', tooltip: 'Number of deliveries to be delivered', 
				title:'<span class="glyphicon glyphicon-shopping-cart"></span><span class="glyphicon glyphicon-hourglass"></span>' }, 
			cnt_delivered: { width: '12', tooltip: 'Number of deliveries actually delivered',
				title:'<span class="glyphicon glyphicon-shopping-cart"></span><span class="glyphicon glyphicon-thumbs-up"></span>' }, 
			is_problem: { width: '2', cb:aDashboardIsProblemCB, title: '<span class="glyphicon glyphicon-info-sign"></span>', 
				tooltip:'You\'ll see a warning sign if a delivery problem happens' }
			}, 
		tableActions: [],
		rowActions: [
			{ title:'Display debetings', cb: aDashboardActionDisplayDPandSupplyDebetings, glyphIcon:'glyphicon glyphicon-shopping-cart' },
		] 
	};
	aRequestDashboardDataArray(`/a_supplies/deliveries/${supply.id}`, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadUsers, arg1:supply, arg2:el }
	);
}


// FARM SECTION ***********************************************************************************************************

function aDashboardActionGetFarmKeyProperties() {
	return { title:{required:true}, descr:{required:true}, address:{required:true}, square:{type:'number'} };
}

function aDashboardActionFormatFarmDetails( farm ) {
	return `<mark><b>${farm.title}</b></mark><br/>${farm.descr}<br/>${farm.address}`;
} 

function aDashboardActionCreateFarm( farm, el ) {
	let windowTitle = `${_myStrFarm} | ${_myStrCreating}`;
	let values = { title: 'Please, provide a title', descr: 'Please, provide a description...', 
		address:'', square:'', prepared_square:'', latitude:'', longitude:'', icon:null };
	let kprops = aDashboardActionGetFarmKeyProperties();

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, { rightPaneHTML:'', keyProperties:kprops, saveURL:'/a_farms_new' } );
}


function aDashboardActionUpdateFarm( farm, el ) {	
	let windowTitle = `${_myStrFarm} <mark>${farm.title}</mark> | ${_myStrUpdating}`;

	let values = { 
		id:farm.id, title: farm.title, descr: farm.descr, address: farm.address, latitude: farm.latitude, longitude: farm.longitude, 
		square: farm.square, prepared_square: farm.prepared_square, icon:farm.icon };
	
	let kprops = aDashboardActionGetFarmKeyProperties();
	kprops.id = { hidden:true };

	let rpane = aDashboardActionFormatFarmDetails(farm);

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, { rightPaneHTML:rpane, keyProperties:kprops, saveURL:'/a_farms_update' } );
}


function aDashboardActionDeleteFarm( farm, el ) {	
	aDisplayDashboardDataArrayEditWindow( `${_myStrFarm} <mark>${farm.title}</mark> | ${_myStrDeleting}`, null, { id:farm.id }, 
		{ leftPaneHTML:`<br/><h1>${_myStrDelete}?</h1>`, rightPaneHTML:aDashboardActionFormatFarmDetails(farm), 
			keyProperties:{ id: { hidden:true } }, saveURL:'/a_farms_delete' } );
}


// DELIVERY POINT SECTION ***********************************************************************************************************

function aDashboardActionGetDPKeyProperties() {
	return { title:{required:true}, descr:{required:true}, address:{required:true}, 
		latitude:{type:'number'}, longitude:{type:'number'} };
}

function aDashboardActionFormatDPDetails( dp ) {
	return `<mark><b>${dp.title}</b></mark><br/>${dp.descr}<br/>${dp.address}`;
} 

function aDashboardActionCreateDeliveryPoint( dp, el ) {
	let windowTitle = `${_myStrDeliveryPoint} | ${_myStrCreating}`;

	let values = { title: 'Please, provide a title', descr: 'Please, provide a description...', 
		address:'', latitude:'', longitude:'', delivery_info:'', pickup_info:'', icon:null };
	
	let kprops = aDashboardActionGetDPKeyProperties();
	let rpane = `<div align=center>Creating a New Delivery Point</div>`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, { rightPaneHTML:rpane, keyProperties:kprops, saveURL:'/a_delivery_points_new' } );
}

function aDashboardActionUpdateDeliveryPoint( dp, el ) {	
	let values = { 
		id:dp.id, title: dp.title, descr: dp.descr, address: dp.address, latitude: dp.latitude, longitude: dp.longitude, 
		delivery_info:dp.delivery_info, pickup_info:dp.pickup_info, icon: dp.icon };
	
	let kprops = aDashboardActionGetDPKeyProperties();
	kprops.id = { hidden:true};
	
	aDisplayDashboardDataArrayEditWindow( `${_myStrDeliveryPoint} <mark>${dp.title}</mark> | ${_myStrUpdating}`, null, values, 
		{ rightPaneHTML:aDashboardActionFormatDPDetails(dp), keyProperties:kprops, saveURL:'/a_delivery_points_update' } );
}

function aDashboardActionDeleteDeliveryPoint( dp, el ) {	
	aDisplayDashboardDataArrayEditWindow( `${_myStrDeliveryPoint} <mark>${dp.title}</mark> | ${_myStrDeleting}`, null, { id:dp.id }, 
		{ leftPaneHTML:`<h1>${_myStrDelete}?</h1>`, rightPaneHTML:aDashboardActionFormatDPDetails(dp), 
			keyProperties:{ id: { hidden:true } }, saveURL:'/a_delivery_points_delete' } );
}


// DELIVERY UNIT SECTION ***********************************************************************************************************

function aDashboardActionCreateDeliveryUnit( du, el ) {
	let windowTitle = `New Delivery Point...`;

	let values = { title: 'A delivery unit', descr: 'The description for the delivery unit...', 
		tonnage:0, volume:0, icon:null };
	
	let keyProperties = {};
	let rightPaneHTML = `<div align=center>Creating a New Delivery Point</div>`;

	let keys = [ 'title', 'descr', 'tonnage', 'volume', 'icon' ];

	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_delivery_units_new' } );
}

function aDashboardActionUpdateDeliveryUnit( du, el ) {	
	let windowTitle = `Edit Delivery Unit <mark>${du.title}</mark>`;

	let values = { 
		id:du.id, title: du.title, descr: du.descr, tonnage: du.tonnage, volume: du.volume, icon: du.icon };
	
	let keyProperties = { id: {hidden:true} };
	
	let rightPaneHTML = `Edit Delivery Unit<br/><mark><b>${du.title}</b></mark><br/>${du.descr}`;

	let keys = [ 'id', 'title', 'descr', 'tonnage', 'volume', 'icon' ];
	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_delivery_units_update' } );
}

function aDashboardActionDeleteDeliveryUnit( du, el ) {	
	let windowTitle = `Deleting the Delivery Unit...`;

	let values = { id:du.id, title: du.title, descr: du.descr };
	
	let keyProperties = { id: { hidden:true }, title: {hidden:true}, descr:{hidden:true} };
	
	let leftPaneHTML = `<b>Deleting Delivery Unit<br/><mark>${du.title}</mark></br><br/>${du.descr}<br/>`+
		`<h1>Are You Sure Deleting This Delivery Unit?</h1>`;

	let keys = [ 'id', 'title', 'descr' ];
	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ leftPaneHTML:leftPaneHTML, rightPaneHTML:null, keyProperties:keyProperties, saveURL:'/a_delivery_units_delete' } );
}


// PERSONS, SLIDESHOW, TEXTS SECTION ************************************************************************************************

function aDashboardActionCreatePerson( p, el ) {
	let windowTitle = `New Person...`;

	let values = { name: 'Please, provide a name', descr: 'Please, provide a description...', position: 'Please, provide a position', icon:null };
	
	let keyProperties = { descr: { height:'200px' } };
	let rightPaneHTML = `<h1 align=center>Creating a New Person</h1>`;

	let keys = [ 'name', 'position', 'descr', 'icon' ];

	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_persons_new' } );
}


function aDashboardActionUpdatePerson( p, el ) {	
	let windowTitle = `Updating Person Information <mark>${p.name}</mark>`;

	let values = { id:p.id, name: p.name, position:p.position, descr: p.descr, icon: p.icon };
	let keyProperties = { id: {hidden:true}, descr: { height:'200px' } };
	
	let rightPaneHTML = `Edit Person<br/><mark><b>${p.name}</b></mark><br/>${p.descr}`;

	let keys = [ 'id', 'name', 'position', 'descr', 'icon' ];
	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_persons_update' } );
}


function aDashboardActionDeletePerson( p, el ) {	
	let windowTitle = `Deleting the Person...`;

	let values = { id:p.id, name: p.name };
	
	let keyProperties = { id: { hidden:true }, name: {readOnly:true} };
	
	let rightPaneHTML = `<b>Deleting Person<br/><mark>${p.name}</mark></br><br/><h1>Are You Sure Deleting This Person?</h1>`;

	let keys = [ 'id' ];
	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_persons_delete' } );
}


function aDashboardActionCreateSlide( s, el ) {
	let windowTitle = `New Slide...`;

	let values = { title: 'Please, provide a title', descr: 'Please, provide a description...', image_url:'Please, provide image url' };
	
	let keyProperties = { descr: { height:'200px' } };
	let rightPaneHTML = `<h1 align=center>Creating a New Slide</h1>`;

	let keys = [ 'title', 'descr', 'image_url' ];

	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_slides_new' } );
}


function aDashboardActionUpdateSlide( s, el ) {	
	let windowTitle = `Updating Slide <mark>${s.title}</mark>`;

	let values = { id:s.id, title: s.title, descr: s.descr, image_url:s.image_url };
	let keyProperties = { id: {hidden:true}, descr: { height:'200px' } };
	
	let rightPaneHTML = `Edit Slide<br/><mark><b>${s.title}</b></mark><br/>${s.descr}` + 
		`<br/><img src='${s.image_url}' style='max-width:200px; max-height:100px;'>`;

	let keys = [ 'id', 'title', 'descr', 'image_url' ];
	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_slides_update' } );
}


function aDashboardActionDeleteSlide( s, el ) {	
	let windowTitle = `Deleting the Slide...`;

	let values = { id:s.id, title: s.title };
	
	let keyProperties = { id: { hidden:true } };
	
	let rightPaneHTML = `<b>Deleting Slide<br/><mark>${s.title}</mark></br><br/><h1>Are You Sure Deleting This Slide?</h1>` + 
		`<br/><img src='${s.image_url}' style='max-width:200px; max-height:100px;'>`;

	let keys = [ 'id' ];
	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_slides_delete' } );
}


function aDashboardActionUpdateText( t, el ) {	
	let windowTitle = `<mark>${t.comment}</mark> | ${_myStrUpdating}`;

	let values = { id:t.id, title: t.title, descr: t.descr, comment: t.comment, lang:_lang };
	let kprops = { id: {hidden:true}, comment: {readOnly:true}, descr: { height:'200px' }, lang:{ hidden:true } };
	
	let rpane = `<mark>${t.comment}</mark><br/>${_myStrUpdating}<br/><br/><mark><b>${t.title}</b></mark>`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rpane, keyProperties:kprops, saveURL:'/a_texts_update' } );
}


function aDashboardActionCreateLink( lnk, el ) {
	let windowTitle = `New Link...`;

	let values = { url: 'Please, provide an url', title: 'Please, provide a title', descr: 'Please, provide a description...', icon:null };
	
	let keyProperties = { descr: { height:'200px' } };
	let rightPaneHTML = `<h1 align=center>Creating a New Link</h1>`;

	let keys = [ 'url', 'title', 'descr', 'icon' ];

	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_links_new' } );
}


function aDashboardActionUpdateLink( lnk, el ) {	
	let windowTitle = `Updating Link <mark>${lnk.title}</mark>`;

	let values = { id: lnk.id, url: lnk.url, title: lnk.title, descr: lnk.descr, icon:lnk.icon };
	let keyProperties = { id: {hidden:true}, descr: { height:'200px' } };
	
	let rightPaneHTML = `Edit Link<br/><mark><b>${lnk.title}</b></mark><br/><b>${lnk.descr}</b><br/>${lnk.descr}`;

	let keys = [ 'id', 'url', 'title', 'descr', 'icon' ];
	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_links_update' } );
}


function aDashboardActionDeleteLink( lnk, el ) {	
	let windowTitle = `Deleting the Link...`;

	let values = { id:lnk.id, title: lnk.title };
	
	let keyProperties = { id: { hidden:true }, title: { readOnly:true } };
	
	let rightPaneHTML = `<b>Deleting Link<br/><mark>${lnk.title}</mark></br><b>${lnk.url}</b><br/><h1>Are You Sure Deleting This Link?</h1>`;
	let keys = [ 'id' ];
	aDisplayDashboardDataArrayEditWindow( windowTitle, keys, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/a_links_delete' } );
}
