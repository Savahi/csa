
function uDashboardActionSwitchSuspend( user, el=null ) {

	// Actually suspends/unsuspends the user for supplies...
	let uDashboardActionSuspend = function( response ) {
		aRequestDataForDashboardEditWindow( '/u_suspend/'+status, 'changing supply status', function(response) {
			if( response.records_affected == 1 ) { 		// Suspended/unsuspended ok...
				myPopupDivHide();
				let el = document.getElementById('uDashboardSuspended');
				el.className = (response.is_suspended_for_supply==1) ? 'glyphicon glyphicon-unchecked' : 'glyphicon glyphicon-check';
				el.dataset.is_suspended_for_supply = response.is_suspended_for_supply;
			} 
			//else 
			//{   // An error occured...
			//	myPopupDivSetMode(1);
			//	myPopupDivSetBody('Failed to change suspend status. Please try again later...');
			//}		
		});
	}
	
	// Requesting data from server to confirm suspend status and inform user about upcoming supplies...
	let status;
	let statusText;
	if(document.getElementById('uDashboardSuspended').dataset.is_suspended_for_supply == 1) {
		status='off';
		statusText=`${_myStrAllow}`;
	} else {
		status='on';
		statusText=`${_myStrPause}`;
	}
	myPopupDivShow(`${_myPageSupplies}`, `<h1>${statusText}?</h1>`, { mode:4, size:'s' } );
	myPopupDivSetMode( 4, uDashboardActionSuspend, status );
}


function uDashboardActionUpdateDeliveryPoint(dp) {
	let cb = function(values, notUsed) {
		location.reload();
		//document.getElementById('aDashboardDPTitle').innerHTML = values.title;
		//document.getElementById('aDashboardDPAddress').innerHTML = values.address;
		//document.getElementById('aDashboardDPDescr').innerHTML = values.descr;
		//document.getElementById('aDashboardDPLatitude').innerHTML = values.latitude;
		//document.getElementById('aDashboardDPLongitude').innerHTML = values.longitude;
		//document.getElementById('aDashboardDPPickupInfo').innerHTML = values.pickup_info;
		//document.getElementById('aDashboardDPDeliveryInfo').innerHTML = values.delivery_info;
	}

	let windowTitle = `${_myStrDeliveryPoint} <mark>${dp.title}</mark> | ${_myStrUpdating}`;

	let values = { 
		id: dp.id, title: dp.title, address: dp.address, descr: dp.descr, 
		latitude: dp.latitude, longitude: dp.longitude, 
		pickup_info: dp.pickup_info, delivery_info: dp.delivery_info };

	let keyProperties = { 
		id: { hidden:true }, title:{ required:true }, 
		descr: { type:'textarea', required:true }, 
		address: { type:'textarea', required:true }, 
		latitude: { type:'number', required:true, title:'Lat.' }, 
		longitude: { type:'number', required:true, title:'Lon' }, 
		pickup_info: {type:'textarea', title:`${_myStrPickupInfo}` }, 
		delivery_info: {type:'textarea', title:`${_myStrDeliveryInfo}` } 
	};

	let rightPaneHTML = `<div align=center>${_myStrUpdating}</br><b><mark>${dp.title}</mark></b></div>`;

	_aDashboardDataAfterSaveCallback = { cb:cb, arg1:values, arg2:null };
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/u_update_delivery_point' } );
}

function uDashboardActionUpdateUserData(user) {
	let cb = function(notUsed1, notUsed2, r) {
		location.reload();
	}

	let windowTitle = `${_myStrUser} <mark>${user.name}</mark> | ${_myStrUpdating}`;

	let values = { id: user.id, name: user.name, contacts: user.contacts, icon:user.icon };

	let keyProperties = { id: { hidden:true }, name: { required:true }, contacts: {type:'textarea', required:true}, icon:{} };

	let contacts = (user.contacts) ? user.contacts : '';
	let rightPaneHTML = `${_myStrUser} <mark>${user.name}</mark><br/><br/>${_myStrContacts}: ${contacts}`;

	_aDashboardDataAfterSaveCallback = { cb:cb, arg1:null, arg2:null };
	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/u_update' } );	
}
