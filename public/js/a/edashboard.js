
function eLoadPublications(el, request=null) {
	let cbIsHidden = function(data,el) {
		el.innerHTML = (data.is_hidden!=1) ? '<span class="glyphicon glyphicon-ok"></span>':'<span class="glyphicon glyphicon-ban-circle"></span>';
	}; 

	let cbTitle = function(data,el) {
		el.innerHTML = `${data.title}`; 
		el.title = `${data.descr}`; 
	};

	let parameters = { 
		display: { 
			id: {width:'3'}, title: { width:'20', cb:cbTitle }, 
			created_at: { width:'8', title:'<span class="glyphicon glyphicon-calendar"></span>' },
			icon: {width:'4'}, 
			is_hidden: { width:'2', cb: cbIsHidden, title:'<span class="glyphicon glyphicon-eye-open"></span>' }
		}, 
		tableActions: [
			{ title: 'Create New Publication', cb: eDashboardActionCreatePublication, glyphIcon:'glyphicon glyphicon-plus' } 
		],
		rowActions: [ 
			{ title: 'Edit', cb: eDashboardActionUpdatePublication, glyphIcon:'glyphicon glyphicon-pencil' }, 	
			{ title: 'Delete', cb: eDashboardActionDeletePublication, glyphIcon:'glyphicon glyphicon-remove' }, 	
		] 
	};
	aRequestDashboardDataArray('/e_publications', 
		function(dataArray) {
			aDisplayDashboardDataArray(el, dataArray, parameters);
		},
		{cb:eLoadPublications, arg1:el, arg2:request }
	);
}
