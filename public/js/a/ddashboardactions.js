
function dDashboardActionDisplayDeliveries( supply, el ) {
	let cbDeliveryPointId = function(dataObject,destElem) {
		let url = `/noauth_delivery_points_short/${dataObject.delivery_point_id}`;
		fetch(url).then(data=>data.json()).then( function(data) { 
			let text = `${data[0].title} / ${data[0].delivery_info}`;
			destElem.innerHTML = 
				`<a href='/delivery_point/${dataObject.delivery_point_id}' target=_blank>${text}</a>`; 
			destElem.title = text;
		} );
	};
	let cbProblem = function( data, el ) {
		if( data.problem ) {
			el.innerHTML = '<span class="glyphicon glyphicon-warning-sign"></span>';
		} else {
			el.innerHTML = '';
		}
	}

	let parameters = { 
		dataTitle: `Supply: "<b><i>${supply.title}</i></b>", ${supply.deliver_to} => Deliveries`,
		display: { 
			delivery_point_id: {width:'20', cb:cbDeliveryPointId }, 
			cnt_deliveries: { width: '10', title:'<span class="glyphicon glyphicon-shopping-cart"></span><span class="glyphicon glyphicon-hourglass"></span>' }, 
			cnt_delivered: { width: '12', title:'<span class="glyphicon glyphicon-shopping-cart"></span><span class="glyphicon glyphicon-thumbs-up"></span>' },
			problem: { width:'2', title:'<span class="glyphicon glyphicon-info-sign"></span>', cb:cbProblem }
			}, 
		tableActions: [],
		rowActions: [
			{ title: 'Report done / not done', cb: dDashboardActionUpdateDelivery, glyphIcon:'glyphicon glyphicon-thumbs-up'},
			{ title: 'Report / revoke problem', cb: dDashboardActionUpdateDeliveryProblem, glyphIcon:'glyphicon glyphicon-thumbs-down'},
		] 
	};
	aRequestDashboardDataArray(`/d_supplies/deliveries/${supply.id}`, 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);			
		},
		{cb:dDashboardActionDisplayDeliveries, arg1:supply, arg2:el}
	);
}


function dDashboardActionUpdateDelivery( delivery, el ) {
	let windowTitle;
	if( delivery.cnt_deliveries > delivery.cnt_delivered ) {
		windowTitle = 'Change Delivery Status to <mark><b>Done</b></mark>';
	} else {
		windowTitle = 'Change Delivery Status to <mark><b>Not done</b></mark>';
	}
	let delivery_text = `<span class="glyphicon glyphicon-map-marker"></span>: <b>${delivery.delivery_point_title}</b><br/>`+
		`<span class="glyphicon glyphicon-shopping-cart"></span>: <b>${delivery.supply_title}</b><br/>` + 
		`<span class="glyphicon glyphicon-calendar"></span>: <b>${delivery.deliver_to}</b>`;
	let status_text;
	let status;
	if( delivery.cnt_deliveries > delivery.cnt_delivered ) {
		status_text = "You are about to change the status of the delivery to DONE. Ok?";
		status = 'delivered';
	} else {
		status_text ="You have alreqdy changed the status of the delivery to DONE. Undo?";
		status = 'pending';
	}
	let leftPaneHTML = `<div style='font-size:120%;'>${delivery_text}</br><br/><i>${status_text}</i></div>`;

	let values = { dp_id: delivery.delivery_point_id, su_id:delivery.supply_id, status:status };
	let keyProperties = { dp_id:{hidden:true}, su_id:{hidden:true}, status:{hidden:true} };

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ leftPaneHTML:leftPaneHTML, keyProperties:keyProperties, saveURL:'/d_update_delivery_status' } );
}
 

function dDashboardActionUpdateDeliveryProblem( delivery, al ) {
	//if( delivery.cnt_deliveries > delivery.cnt_delivered ) {
	//	alert('Alreay relivered. First change the status to "Not down"!');
	//	return;
	//}
	let windowTitle = `Reporting a Delivery Problem`;

	let values = { dp_id:delivery.delivery_point_id, su_id:delivery.supply_id, problem:delivery.problem };
	let keyProperties = { dp_id: {hidden:true}, su_id: { hidden:true }, problem:{type:'textarea', height:'200px'} };
	
	let rightPaneHTML = `<span class="glyphicon glyphicon-map-marker"></span>: <b>${delivery.delivery_point_title}</b><br/>` + 
		`<span class="glyphicon glyphicon-shopping-cart"></span>: <b>${delivery.supply_title}</b><br/>` +
		`<span class="glyphicon glyphicon-calendar"></span>: <b>${delivery.deliver_to}</b>`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/d_update_delivery_problem' } );
}
