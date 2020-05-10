
function aLoadUsers(el, request=null) {
	let cbBalance = function(data,el) {
		let deposit = (data.deposit!==undefined) ? ('&nbsp;/ ' + data.deposit) : '';
		el.innerHTML = `${data.balance}${deposit}`; 
	}
	let display = { id: { width:'3' }, name: { width:'10' }, email: { width:'10' }, 
		contacts: { width:'15' }, icon: { width:40, load_from_table:'users' } };
	let filters = { name: {type:'text'}, email: {type:'text'}, minbalance: {type:'number'}, mindeposit: {type:'number'} };
	if( request === null ) {
		display['balance'] = { width: '10', cb:cbBalance, title: `${_myStrBalance}&nbsp;/ ${_myStrDeposit}, ${_myConstCurrencyUnit}`};
		display['is_suspended'] = { title:`${_myStrNotSuspended}?`,  
			cb: function(d,e) { 
				let notSusp = (d.is_suspended_for_supply!=1);
				e.innerHTML = '<span class="glyphicon glyphicon-shopping-cart"></span>' + 
					((notSusp) ? '<span class="glyphicon glyphicon-thumbs-up"></span>' : '<span class="glyphicon glyphicon-thumbs-down"></span>');
			}
		};
		display['delivery_point_id'] = { width: '8', cb: aRequestDeliveryPointDataForDashboardCell };
	} 
	if( request === 'delivery_point_admin' ) {
		display['delivery_point_id'] = { width: '8', cb: aRequestDeliveryPointDataForDashboardCell };
	}
	let rawActions = [ { title: 'Edit', cb: aDashboardActionUpdateUser, glyphIcon:'glyphicon glyphicon-pencil' } ];
	if( request === null /*|| request === 'delivery_point_admin'*/ ) {
		rawActions.push( { title: `${_myStrRefilling}`, cb: aDashboardActionRefillUser, innerHTML: '<b>+</b>&#36;' } ); 	
		//rawActions.push( { title: `${_myStrDebeting}`, cb: aDashboardActionDebetUser, innerHTML: '<b>-</b>&#36;' } ); 	
		rawActions.push( { title: _myStrRefills, cb: aDashboardActionDisplayUserRefills, 
			innerHTML:'&#36;<span class="glyphicon glyphicon-piggy-bank"></span>' } ); 	
		rawActions.push( { title: _myStrDebetings, cb: aDashboardActionDisplayUserDebetings, 
			innerHTML:'&#36;<span class="glyphicon glyphicon-shopping-cart"></span>' } );
	}
	if( request === 'all' || request === 'not_assigned_to_dp' ) {
		rawActions.push( { title: 'Delete', cb: aDashboardActionDeleteUser, glyphIcon:'glyphicon glyphicon-remove' } );
	}
	let parameters = { 
		display: display, 
		tableActions: [],
		rowActions: rawActions 
	};
	let url = '/a_users';
	if( request !== null ) {
		url += '/' + request;
	}
	aRequestDashboardDataArray( url, 
		function( dataArray ) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadUsers, arg1:el, arg2:request }, filters
	);
}

function aLoadDeliveryUnits(el, request=null) {
	parameters=null;
	aRequestDashboardDataArray('/a_delivery_units', 
		function(dataArray) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadDeliveryUnits, arg1:el, arg2:request }
	);
}


function aLoadDeliveryAssignments(el, request=null) {
	aRequestDashboardDataArray('/a_delivery_assignments', 
		function(dataArray, parameters) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadDeliveryAssignments, arg1:el, arg2:request }
	);
}


function aLoadSortingStations(el, request=null) {
	parameters=null;
	aRequestDashboardDataArray('/a_sorting_stations', 
		function(dataArray) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadSortingStations, arg1:el, arg2:request }
	);
}

function aLoadCrops(el, request=null) {
	let cbId = function( dataObject, destElem ) { 
		let url = `/a_cultivation_assignments/cr/${dataObject.id}`;
		fetch(url).then(data=>data.json()).then( 
			function(data) { 
					destElem.innerHTML = data.length; 
					destElem.title = `${_myStrCultivations} (${_myStrOverall}): ${data.length}`;
			} 
		).catch( function(e) { destElem.innerHTML='?'; } );
  	};

	let cbTitle = function(data,el) { 
		el.innerHTML=`<a href='/crop/${data.id}' target=_blank>${data.title}</a>`; 
	};	
	let parameters = { 
		display: { 
			id:{ width:'3', cb:cbId, title:'<span class="glyphicon glyphicon-grain"></span>', tooltip:`${_myStrCultivations} (${_myStrOverall})`}, 
			icon: { width:'10%' }, title: { width:'40%', cb:cbTitle }, descr: {}
		}, 
		tableActions: [			
			{ title: `${_myStrCreate}`, cb: aDashboardActionCreateCrop, glyphIcon:'glyphicon glyphicon-plus' } 
		],
		rowActions: [ 
			{ title: `${_myStrUpdate}`, cb: aDashboardActionUpdateCrop, glyphIcon:'glyphicon glyphicon-pencil' }, 	
			{ title: `${_myStrAdd} ${_myStrCultivation}`, cb: aDashboardActionCreateCultivationAssignment, glyphIcon:'glyphicon glyphicon-grain'}, 	
			{ title: `${_myStrCultivations}`, cb: aDashboardActionShowCultivationAssignments, glyphIcon:'glyphicon glyphicon-list' }, 
			{ title: `${_myStrDelete}`, cb: aDashboardActionDeleteCrop, glyphIcon:'glyphicon glyphicon-remove' } 	
		] 
	};
	let filters = { title: {type:'text'} };

	aRequestDashboardDataArray('/noauth_crops', 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadCrops, arg1:el, arg2:request }, filters
	);
}


function aLoadCultivationAssignments(el, settings=null) {
	let cbTitle = function(data,el) {
		let text = `${data.title} / ${data.descr}`;
		el.innerHTML = `${text}`; 
		el.title = `${text}`; 
	}
	let cbAmount = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.amount_by_plan, data.amount_prognosed, data.amount_actual);
		el.innerHTML = `${r[0]} / ${data.reserved}`;
		el.title = `${r[1]} / ${data.reserved}`;
	};
	let cbDateStart = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.start_by_plan, data.start_prognosed, data.start_actual);
		//el.innerHTML = `${data.start_by_plan} / ${data.start_prognosed} / ${data.start_actual}`;
		el.innerHTML = `<a href='/workflow/by_ca_id/${data.id}' target=_blank>${r[0]}</a>`;
		el.title= r[1];
	};
	let cbDateFinish = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.finish_by_plan, data.finish_prognosed, data.finish_actual);
		//el.innerHTML = `${data.finish_by_plan} / ${data.finish_prognosed} / ${data.finish_actual}`;
		el.innerHTML = `<a href='/workflow/by_ca_id/${data.id}' target=_blank>${r[0]}</a>`;
		el.title= r[1];
	};
	let cbId = function( data, destElem ) { 
		let text = `${data.farm_title} (${data.square}/${data.farm_square})`;
		destElem.innerHTML = `<a href='/a_workflow/by_ca_id/${data.farm_id}' target=_blank>${text}</a>`; 
		destElem.title = text;
  	};
	let parameters = { 
		display: { 
			title: { cb:cbTitle }, 
			is_finished: { cb: function(d,e) { e.innerHTML = aDashboardHelperFormatDoneOrNotDoneCell(d.is_finished==1); } },
			is_accepted: { cb: function(d,e) { e.innerHTML = aDashboardHelperFormatDoneOrNotDoneCell(d.is_accepted==1); } },
			id: { width:'14', cb:cbId, title:`${_myPageFarms} (${_myConstSquareUnitHTML}/${_myStrOverall})` }, 
			amount_by_plan: { cb:cbAmount, title:aDashboardGetPlanProgActResAmountTitle() }, 
			start_by_plan: { cb:cbDateStart, title:aDashboardGetPlanProgActStartTitle() }, 
			finish_by_plan: { cb:cbDateFinish, title:aDashboardGetPlanProgActFinishTitle() } 
		 }, 
		tableActions: [],
		rowActions: [ 
			{ title: 'Delete Cultivation Assignment', cb: aDashboardActionDeleteCultivationAssignment, glyphIcon:'glyphicon glyphicon-remove'}, 	
			{ title: 'Edit Cultivation Assignment', cb: aDashboardActionUpdateCultivationAssignment, glyphIcon:'glyphicon glyphicon-pencil' }, 	
			{ title: 'Operations', cb: aDashboardActionDisplayOperations, glyphIcon:'glyphicon glyphicon-tasks' }, 	
			{ title: 'Create Harvesting Assignment', cb: aDashboardActionCreateHarvestingAssignment, glyphIcon:'glyphicon glyphicon-apple'}, 	
		] 
	};
	let url = '/a_cultivation_assignments';
	aRequestDashboardDataArray(url, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray,parameters);			
		},
		{cb:aLoadCultivationAssignments, arg1:el, arg2:settings}
	);
}


function aLoadHarvestingAssignments(el, settings=null) {
	let cbTitle = function(data,el) {
		el.innerHTML=`${data.title} / ${data.descr}`; 
	}
	let cbCaTitle = function( dataObject, destElem ) { 
		let text = `${dataObject.ca_finish_prognosed} / ${dataObject.ca_title}`;
		destElem.innerHTML=`<a href='/workflow/by_supply_id/${dataObject.supply_id}' target=_blank>${text}<a/>`; 
		destElem.title = text;
  	};
	let cbSupply = function( dataObject, destElem ) { 
		let text = `${dataObject.su_deliver_to} / ${dataObject.su_title}`;
		destElem.innerHTML=`<a href='/workflow/by_supply_id/${dataObject.supply_id}' target=_blank>${text}</a>`; 
		destElem.title = text;
  	};
	let cbAmount = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.amount_by_plan, data.amount_prognosed, data.amount_actual);
		el.innerHTML = r[0];
		el.innerTitle = r[1];
	};
	let cbDateFinish = function( data, el ) {
		let r = aDashboardFormatPlanProgActHTML(data.finish_by_plan, data.finish_prognosed, data.finish_actual);
		el.innerHTML = `<a href='/workflow/by_ca_id/${data.id}' target=_blank>${r[0]}</a>`;
		el.title= r[1];
	};
	let parameters = { 
		display: { title: { width:'20%', cb:cbTitle }, 
			is_finished: { width:'2', cb: function(d,e) { e.innerHTML = aDashboardHelperFormatDoneOrNotDoneCell(d.is_finished==1); } },
			is_accepted: { width:'2', cb: function(d,e) { e.innerHTML = aDashboardHelperFormatDoneOrNotDoneCell(d.is_accepted==1); } },
			amount_by_plan: { width:'12', cb:cbAmount, title:aDashboardGetPlanProgActAmountTitle() },
			finish_by_plan: { width:'24', cb:cbDateFinish, title:aDashboardGetPlanProgActFinishTitle() },
			supply_id:{ width:'20', cb: cbSupply, title:`${_myPageSupplies}` }, 
			ca_title: { width:'20', cb:cbCaTitle, title:`${_myStrCultivations}`} }, 
		tableActions: [],
		rowActions: [ 
			{ title: 'Delete Harvesting Assignment', cb: aDashboardActionDeleteHarvestingAssignment, glyphIcon:'glyphicon glyphicon-remove'}, 	
			{ title: 'Edit Harvesting Assignment', cb: aDashboardActionUpdateHarvestingAssignment, glyphIcon:'glyphicon glyphicon-pencil'}, 	
			//{ title: 'Select For Appending to a Supply', cb:'checkbox', glyphIcon:'glyphicon glyphicon-plus'}, 	
		] 
	};
	let url = '/a_harvesting_assignments';
	aRequestDashboardDataArray(url, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadHarvestingAssignments, arg1:el, arg2:settings}
	);
}

function aLoadSupplies(el, settings=null) {
	let cbTitle = function(data,el) {
		el.innerHTML=`${data.title}<br/>${data.descr}`; 
	}
	let cbDeliverTo = function(data,el) {
		el.innerHTML=`<a href='/workflow/by_supply_id/${data.id}' target=_blank>${data.deliver_to}</a>`; 
		el.style.whiteSpace = 'nowrap';
	}
	let cbId = function( dataObject, destElem ) { 
		let url = `/a_supplies/deb/${dataObject.id}/${dataObject.price_per_user}`;
		fetch(url).then(data=>data.json()).then( 
			function(data) { 
				destElem.innerHTML = `&nbsp;&nbsp;&nbsp;&nbsp;${data[0].nd}/${data[0].nnd}&nbsp;&nbsp;&nbsp;&nbsp;`; 
				destElem.title = `Debeted:${data[0].nd} / Not debeted: ${data[0].nnd}`; 
			}).catch( function(e) { destElem.innerHTML='?/?'; } );
  	};

	let parameters = { 
		display: { 
			id:{ width:'7', cb:cbId, tooltip:'Number of Users: Debeted / Not Debeted',
				title:'<span class="glyphicon glyphicon-user"></span>&nbsp;&#47;&nbsp;<span class="glyphicon glyphicon-user" style="color:gray;"></span>' }, 
			deliver_to: { width:'16', cb:cbDeliverTo }, title: { width:'20', cb:cbTitle }, 
			icon: {width:'4'}, price_per_user: {width:'10', tooltip:'Price for a user'} 
		}, 
		tableActions: [ 
			{ title: 'Create New Supply From Scratch', cb: aDashboardActionCreateSupply, glyphIcon:'glyphicon glyphicon-plus' } 
		],
		rowActions: [ 
			{ title: 'Delete Supply', cb: aDashboardActionDeleteSupply, glyphIcon:'glyphicon glyphicon-remove'}, 	
			{ title: 'Edit Supply', cb: aDashboardActionUpdateSupply, glyphIcon:'glyphicon glyphicon-pencil'},
			{ title: 'Debet For Supply', cb: aDashboardActionDebetForSupply, glyphIcon:'glyphicon glyphicon-usd'},
			{ title: 'Revoke Debetings For Supply', cb: aDashboardActionRevokeDebetingsForSupply, glyphIcon:'glyphicon glyphicon-minus'},
			{ title: 'Deliveries', cb: aDashboardActionDisplayDeliveries, glyphIcon:'glyphicon glyphicon-shopping-cart'},
		] 
	};
	let url = '/a_supplies';
	aRequestDashboardDataArray(url, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);
		},
		{cb:aLoadSupplies, arg1:el, arg2:settings}
	);
}


function aLoadFarms(el, request=null) {
	let cbTitle = function( data, el ) {
		el.innerHTML = `<a href='/farm/${data.id}' target=_blank>${data.title}</a><br/>${data.descr}`;
	};
	let cbLatitude = function(data, el) {
		el.innerHTML = `${data.latitude} / ${data.longitude}`;
	};
	let cbSquare = function (data, el) {
		el.innerHTML = `${data.square} ${_myConstSquareUnitHTML}`;
	};
	let parameters = { 
		display: { 
			title: { width:'24', cb:cbTitle }, address: { width:'20', tooltip:'Address'}, 
			latitude: { width:'14', cb:cbLatitude, title:'Lat./Lon.', tooltip:'Latitude / Longitude' },
			square: { width:'14', cb:cbSquare },
			icon: {width:'4'}
		}, 
		tableActions: [ 
			{ title: 'Create New Farm', cb: aDashboardActionCreateFarm, glyphIcon:'glyphicon glyphicon-plus' } 
		],
		rowActions: [ 
			{ title: 'Delete Farm', cb: aDashboardActionDeleteFarm, glyphIcon:'glyphicon glyphicon-remove'}, 	
			{ title: 'Edit Farm', cb: aDashboardActionUpdateFarm, glyphIcon:'glyphicon glyphicon-pencil'},
		] 
	};

	aRequestDashboardDataArray('/a_farms', 
		function(dataArray) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadFarms, arg1:el, arg2:request }
	);
}

function aLoadDeliveryPoints(el, request=null) {
	let cbTitle = function( data, el ) {
		el.innerHTML = `<a href='/delivery_point/${data.id}' target=_blank>${data.title}</a><br/>${data.descr}`;
	};
	let cbLatitude = function(data, el) {
		el.innerHTML = `${data.latitude} / ${data.longitude}`;
	}
	let parameters = { 
		display: { 
			title: { cb:cbTitle }, address: {}, latitude: { cb:cbLatitude, title:'Lat./Lon.', tooltip:'Latitude / Longitude' },
			delivery_info: {}, pickup_info: {}, 
			users_count: { title:'<span class="glyphicon glyphicon-user"></span><span class="glyphicon glyphicon-user"></span>'}, icon: {}
		}, 
		tableActions: [ 
			{ title: 'Create New Delivery Point', cb: aDashboardActionCreateDeliveryPoint, glyphIcon:'glyphicon glyphicon-plus' } 
		],
		rowActions: [ 
			{ title: 'Edit Delivery Point', cb: aDashboardActionUpdateDeliveryPoint, glyphIcon:'glyphicon glyphicon-pencil'},
			{ title: 'Delete Delivery Point', cb: aDashboardActionDeleteDeliveryPoint, glyphIcon:'glyphicon glyphicon-remove'}
		] 
	};
	aRequestDashboardDataArray('/a_delivery_points', 
		function(dataArray) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadDeliveryPoints, arg1:el, arg2:request }
	);
}


function aLoadDeliveryUnits(el, request=null) {
	let cbTitle = function( data, el ) {
		let text = `${data.title} / ${data.descr}`;
		el.innerHTML = `<a href='/delivery_unit/${data.id}' target=_blank>${text}</a>`;
		el.title = text;
	};
	let parameters = { 
		display: {
			id: {width:'2' }, 
			title: { width:'24', cb:cbTitle, title:'Title / Descr.' }, 
			tonnage: { width:'8', title:'Tonnage' },
			volume: { width:'8', title:'Volume' },
			icon: {width:'4'}
		}, 
		tableActions: [ 
			{ title: 'Create New Delivery Unit', cb: aDashboardActionCreateDeliveryUnit, glyphIcon:'glyphicon glyphicon-plus' } 
		],
		rowActions: [ 
			{ title: 'Delete Delivery Unit', cb: aDashboardActionDeleteDeliveryUnit, glyphIcon:'glyphicon glyphicon-remove'}, 	
			{ title: 'Edit Delivery Unit', cb: aDashboardActionUpdateDeliveryUnit, glyphIcon:'glyphicon glyphicon-pencil'}
		] 
	};
	aRequestDashboardDataArray('/a_delivery_units', 
		function(dataArray) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadDeliveryUnits, arg1:el, arg2:request }
	);
}


function aLoadPersons(el, request=null) {
	let display = { name: { title:'<span class="glyphicon glyphicon-user"></span>'}, position: {},  descr: {}, icon: {} };

	let rawActions = [ 
		{ title: 'Delete', cb: aDashboardActionDeletePerson, glyphIcon:'glyphicon glyphicon-remove'}, 	
		{ title: 'Edit', cb: aDashboardActionUpdatePerson, glyphIcon:'glyphicon glyphicon-pencil' } ];
	let tableActions = [ { title: 'Create New Person', cb: aDashboardActionCreatePerson, glyphIcon:'glyphicon glyphicon-plus' } ];

	let parameters = { 
		display: display, 
		tableActions: tableActions,
		rowActions: rawActions 
	};
	let url = '/a_persons';

	aRequestDashboardDataArray( url, 
		function( dataArray ) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadPersons, arg1:el, arg2:request }
	);
}


function aLoadSlides(el, request=null) {
	let display = { title: {}, descr:{}, image_url: {} };

	let rawActions = [ 
		{ title: 'Delete', cb: aDashboardActionDeleteSlide, glyphIcon:'glyphicon glyphicon-remove'}, 	
		{ title: 'Edit', cb: aDashboardActionUpdateSlide, glyphIcon:'glyphicon glyphicon-pencil' } ];
	let tableActions = [ { title: 'Create New Slide', cb: aDashboardActionCreateSlide, glyphIcon:'glyphicon glyphicon-plus' } ];

	let parameters = { 
		display: display, 
		tableActions: tableActions,
		rowActions: rawActions 
	};
	let url = '/a_slides';

	aRequestDashboardDataArray( url, 
		function( dataArray ) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadSlides, arg1:el, arg2:request }
	);
}


function aLoadTexts(el, request=null) {
	let cbTitle = function( data, el ) {
		let text = data.title;
		if( data.descr.length > 0 ) {
			text += `<br/>${data.descr}`;
		}
		el.innerHTML = text;
		el.title = text;
	};
	let display = { title: { cb:cbTitle }, comment: {} };

	let rawActions = [ { title: 'Edit', cb: aDashboardActionUpdateText, glyphIcon:'glyphicon glyphicon-pencil' } ];

	let parameters = { 
		display: display, 
		tableActions: [],
		rowActions: rawActions 
	};
	let url = `/a_texts/${_lang}`;

	aRequestDashboardDataArray( url, 
		function( dataArray ) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadTexts, arg1:el, arg2:request }
	);
}


function aLoadLinks(el, request=null) {
	let display = { title: {}, descr:{}, icon: {} };

	let rawActions = [ 
		{ title: 'Delete', cb: aDashboardActionDeleteLink, glyphIcon:'glyphicon glyphicon-remove'}, 	
		{ title: 'Edit', cb: aDashboardActionUpdateLink, glyphIcon:'glyphicon glyphicon-pencil' } ];
	let tableActions = [ { title: 'Create New Person', cb: aDashboardActionCreateLink, glyphIcon:'glyphicon glyphicon-plus' } ];

	let parameters = { 
		display: display, 
		tableActions: tableActions,
		rowActions: rawActions 
	};
	let url = '/a_links';

	aRequestDashboardDataArray( url, 
		function( dataArray ) {
			if( _aDashboardDataArray === null ) {
				return;
			} 
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:aLoadLinks, arg1:el, arg2:request }
	);
}

