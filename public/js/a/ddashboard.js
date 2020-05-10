
function dLoadSupplies(el, request=null) {
	let cbDeliverTo = function(data,elem) {
		elem.innerHTML = data.deliver_to;
		elem.title = data.deliver_to;
	};
	let cbTitle = function(data,elem) {
		let text = data.title + " / " + data.descr;
		elem.innerHTML = text;
		elem.title = text;
	};
	let parameters = { 
		display: { 
			is_delivered: { width:'2', 
				cb: function(d,e) { 
					e.innerHTML=(d.is_delivered==1)?'<span class="glyphicon glyphicon-ok"></span>':'<span class="glyphicon glyphicon-hourglass"></span>' } },
			deliver_to: {width:'14', cb:cbDeliverTo }, delivery_info: {width:'24' }, title: {width:'24', cb:cbTitle}, icon: { width:80 } 
		}, 
		tableActions: [],
		rowActions: [ 
			{ title: 'Deliveries', cb: dDashboardActionDisplayDeliveries, glyphIcon:'glyphicon glyphicon-shopping-cart'}
		] 
	};
	aRequestDashboardDataArray('/d_supplies/pending', 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);
		},
		{cb:dLoadSupplies, arg1:el, arg2:request }
	);
}
