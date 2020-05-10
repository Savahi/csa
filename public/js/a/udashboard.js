
function uLoadSupplies(el, request=null) {
	let cbDPId = function(data,elem) {
		elem.innerHTML = (data.delivery_point_title.length > 0) ? `<a href='/delivery_point/${data.delivery_point_id}'>${data.delivery_point_title}</a>` : '-';
	};
	let cbTitle = function(data,elem) { 
		elem.innerHTML = `<a href='/supply/${data.id}' target=_blank>${data.title}</a><br/>${data.descr}`; 
	};
	let cbDeliverTo = function(data,elem) { 
		elem.innerHTML = `<a href='/workflow/by_supply_id/${data.id}' target=_blank>${data.deliver_to}</a>`; 
	};
	let cbIsProblem = function(data,elem) {
		if( data.is_problem == 1 ) {
			elem.innerHTML = `${data.problem}`; // `<span class="glyphicon glyphicon-warning-sign"></span>`;
			elem.title = `${data.problem}`; 
		} else {
			elem.innerHTML = '<span class="glyphicon glyphicon-thumbs-up"></span>';
			elem.title = 'Ok';
		} 
	}
	let parameters = { 
		display: { 
			amount_debeted: { width:'5', title:`${_myStrDebetings}, ${_myConstCurrencyUnit}`, tooltip:`${_myStrAmountOfMoney}` },
			delivery_point_id: { width:'14', cb:cbDPId, title:`${_myStrDeliveryPoint}` }, 
			deliver_to: { width:'10', cb:cbDeliverTo }, 
			title: { width: '20', cb:cbTitle },
			is_problem: { width: '2', cb:cbIsProblem, title: '<span class="glyphicon glyphicon-info-sign"></span>', 
				tooltip:`${_myStrDeliveryProblem}?` }
			}, 
		tableActions:[], 
		rowActions:[] 
	};
	if( request === null ) {
		request = '/u_supplies/pending';
	}
	aRequestDashboardDataArray( request, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);
		}
	);
}


function uLoadRefills(el, request=null) {
	let parameters = { 
		display: { 
			amount: { width:'10', title:`${_myStrAmountOfMoney}, ${_myConstCurrencyUnit}` }, 
			made_at: { width:'12' }, title: { width: '24' }
		}, 
		tableActions:[], 
		rowActions:[] 
	};
	aRequestDashboardDataArray('/u_refills', 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);
		}
	);
}


function uLoadDebetings(el, request=null) {
	let parameters = { 
		display: { 
			made_at: { width:'10' },
			amount: {width:'10', title:`Amount, ${_myConstCurrencyUnit}` }, 
			supply_id: { width:'14', cb: aRequestSupplyDataForDashboardCell },
			delivery_point_id: {width:'14', cb: aRequestDeliveryPointDataForDashboardCell }
			}, 
		tableActions:[], 
		rowActions:[] 
	};
	aRequestDashboardDataArray('/u_debetings', 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);
		}
	);
}


function uLoadUsers(el, request=null) {
	let cb = function(data,el) { 
		let isNotSusp = (data.is_suspended_for_supply!=1);
		el.innerHTML = '<span class="glyphicon glyphicon-shopping-cart"></span>' + 
			((isNotSusp) ? '<span class="glyphicon glyphicon-thumbs-up"></span>' : '<span class="glyphicon glyphicon-thumbs-down"></span>');
	}
	let parameters = { 
		display: { 
			name: {}, email: {}, 
			balance: { width:'6', title:`${_myStrBalance}, ${_myConstCurrencyUnit}` }, contacts: { title:`${_myStrContacts}` }, 
			is_suspended_for_supply: { cb:cb, title:`${_myStrNotSuspended}?` }, 
			icon: {}
			}, 
		tableActions:[], 
		rowActions:[] 
	};
	if( request === null ) {
		request = '/u_delivery_point_users';
	}
	aRequestDashboardDataArray( request, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);
		},
		{cb:uLoadUsers, arg1:el, arg2:request }
	);
}
