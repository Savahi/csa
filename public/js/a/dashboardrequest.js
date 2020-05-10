
var _aDashboardDataArrayDiv = null;
var _aDashboardDataArray = null;
var _aDashboardDataArrayProperties = null; 	// Properties that come from the server
var _aDashboardDataArrayDisplayParameters = null; 	// Display parameters for each dataset

var _aDashboardTimeout = null;
var _aRequestDataWindowTitle = 'Loading Data';
var _aRequestDataLoadingMessage = 'Please wait while loading data...';
var _aRequestDataParsingError = 'Data loaded are invalid.<br/>Please inform the admin...';
var _aRequestDataLoadingError = 'Failed to load data. No internet connection?<br/>Please try again later...';

var _aDashboardDataPageSize = 50;

function aSetRequestDashboardDataArrayErrorMessage( msg=null ) {
	if( _aDashboardTimeout !== null ) {
		clearTimeout(_aDashboardTimeout);
		_aDashboardTimeout = null;
		if( msg !== null ) {
			myPopupDivShow(_aRequestDataWindowTitle, msg, { mode:1, size:'s' } );
			myPopupDivSetMode(1);
		} else {
			myPopupDivHide();
		}
	} else {
		if( msg !== null ) {
			myPopupDivSetBody(msg);
			myPopupDivSetMode(1);
		} else {
			myPopupDivHide();
		}
	}
}


var _aDashboardRequestStack = {'default': [] };
var _aDashboardRequestStackActiveSection = _aDashboardRequestStack['default'];
var _aDashboardRequestStackBackButton=null;

function aRequestStackSetBackButton(id) {
	_aDashboardRequestStackBackButton = document.getElementById(id);	
}

function aRequestStackAddSection( section ) {
	_aDashboardRequestStack[section] = [];	
}

function aRequestStackSetActiveSection( section ) {
	_aDashboardRequestStackActiveSection = _aDashboardRequestStack[ (section in _aDashboardRequestStack) ? section : 'default' ];	
	aRequestStackUpdateBackButtonDisplay( _aDashboardRequestStackActiveSection );
}


function aRequestStackUpdateBackButtonDisplay() {
	//console.log(_aDashboardRequestStackActiveSection.length);
	if( _aDashboardRequestStackBackButton ) {
		if( _aDashboardRequestStackActiveSection.length >= 2 ) {
			_aDashboardRequestStackBackButton.className = 'a-dashboard-back-button-active';
		} else {
			_aDashboardRequestStackBackButton.className = 'a-dashboard-back-button-disabled';
		}
	}
}

function aRequestStackBack() {
	let stack = _aDashboardRequestStackActiveSection;
	if( stack.length >= 2 ) {
		stack.pop();
		let it = stack[ stack.length-1 ];
    	aRequestDashboardDataArray( it.url, it.cb, it.afterSave, it.filters, true );
	}
	aRequestStackUpdateBackButtonDisplay();
}

function aRequestStackPush( it ) {
	let stack = _aDashboardRequestStackActiveSection;
	if( stack.length > 20 ) {
		stack.shift();
	}		
	stack.push(it);
	aRequestStackUpdateBackButtonDisplay();
}


function aRequestStackRunLatestOrDefault( defaultCallBack ) {
	if( _aDashboardRequestStackActiveSection.length >= 1 ) {
		it = _aDashboardRequestStackActiveSection[ _aDashboardRequestStackActiveSection.length - 1 ];
	    aRequestDashboardDataArray( it.url, it.cb, it.afterSave, it.filters, true );
	} else {
		defaultCallBack();
	}
}


function aRequestDashboardDataArray(url, cb, afterSave=null, filters=[], back=false) {

	if( !back )	// Updating the stack
		aRequestStackPush( { url:url, cb:cb, afterSave:afterSave, filters:filters } );

	if( afterSave != null ) {
		_aDashboardDataAfterSaveCallback = afterSave;
	} else {
		_aDashboardDataAfterSaveCallback = null;
	}

	if( _aDashboardDataArrayDiv === null ) { 		// If dashboard data div is not initialied yet;
		_aDashboardDataArrayDiv = document.getElementById('aDashboardDataArray');
		//_aDashboardDataArrayDiv.style.height = Math.floor(window.innerHeight*0.75) + 'px';
	}
	if( _aDashboardDataArrayDiv === null ) {
		return;
	}

	while( _aDashboardDataArrayDiv.hasChildNodes() ) { 		// Deleting previousely created child nodes...
		_aDashboardDataArrayDiv.removeChild( _aDashboardDataArrayDiv.lastChild );
	}
		
	if( _aDashboardDataArray !== null ) { 		// Unsetting previously loaded data...
		_aDashboardDataArray = null;
	}

	if( filters !== null ) {
		aDashboardAppendDataFilters(filters, url, cb, afterSave );
	}

	aDashboardDisplayPaginationElements(true);

	if( _aDashboardTimeout === null ) {
		_aDashboardTimeout = setTimeout( 
			function() { 
				_aDashboardTimeout = null;			
				myPopupDivShow(_aRequestDataWindowTitle, _aRequestDataLoadingMessage, { size:'s', mode:0 } );
			}, 250
		);
	}

	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 ) {
//alert(this.responseText);
			if( this.status == 200) {
				let data;
				try {
					data = JSON.parse(this.responseText);
				} catch(e) {
					_aDashboardDataArray = null;
					aSetRequestDashboardDataArrayErrorMessage(_aRequestDataParsingError);
					return;
				}		
				aSetRequestDashboardDataArrayErrorMessage();
				if( !('data' in data) ) {			 	
					document.getElementById('aDashboardDataFilter').style.display = 'none';
					_aDashboardDataArray = data;
				} else {
					_aDashboardDataArray = data.data;
					if( 'pagination' in data ) {
						aDashboardAppendDataFiltersWithPaginationData(data.pagination);
					} else {
						document.getElementById('aDashboardDataFilter').style.display = 'none';
					}
				}
     			cb(_aDashboardDataArray);
	    	} else {
				_aDashboardDataArray = null;
				aSetRequestDashboardDataArrayErrorMessage(_aRequestDataLoadingError);
				return;
			}
		}		
	};
	url = aDashboardAppendDataRequestWithFilters(url);
	xhttp.open("GET", url, true);
	xhttp.send();
}


function aRequestDataForDashboardEditWindow( url, requestName, cb ) {

	let errorFunction = function(msg) { 				
		myPopupDivSetMode(1);
		let errorMessageElem = document.getElementById('myPopupDivErrorMessage');		
		if( errorMessageElem !== null ) {
			errorMessageElem.innerText = msg;
		}
		//let okButton = document.getElementById('myPopupDivOk');		
		//if( okButton !== null ) {
		//	okButton.disabled = true;
		//}
	}

	let timeOut = null;
	if( timeOut === null ) {
		timeOut = setTimeout( 
			function() { 
				timeOut = null; 
			}, 
			500 );
	}

	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 ) {
			if( this.status == 200) {
				let response = null;
				try {
					response = JSON.parse(this.responseText);
					cb(response);
				} catch(e) {
					errorFunction('An error occured while ' + requestName + '.');
					return null;
				}					 	
	    	} else {
				errorFunction('Please wait while ' + requestName + '...');
				return null;
			}
		}		
	};
	xhttp.open("GET", url, true);
	xhttp.send();
}


function aRequestDeliveryPointDataForDashboardCell( dataObject, destElem ) { 
	let url = `/noauth_delivery_points_short/${dataObject.delivery_point_id}`;
	fetch(url).then(data=>data.json()).then( function(data) { 
		destElem.innerHTML=`<a href='/delivery_point/${dataObject.delivery_point_id}' target=_blank>${data[0].title}</a>`; 
	}).catch( function(e) { destElem.innerHTML='?'; } );
}

function aRequestSupplyDataForDashboardCell( dataObject, destElem ) { 
	let url = `/noauth_supply_short/${dataObject.supply_id}`;
	fetch(url).then(data=>data.json()).then( function(data) { 
		destElem.innerHTML=`<a href='/supply/${dataObject.supply_id}' target=_blank>${data[0].title}</a>`; 
	} );
}


function aDashboardAppendDataFiltersWithPaginationData( pagination ) {
	let container = document.getElementById('aDashboardDataFilter');
	if( container.style.display === 'none' ) 
		container.style.display = 'block';
	let elOffset = document.getElementById('aDashboardFilterOffset');
	let elOffsetPlusLimit = document.getElementById('aDashboardFilterOffsetPlusLimit');
	let elTotal = document.getElementById('aDashboardFilterTotal');
	if( pagination.total > 0 ) {
		elOffset.innerText = pagination.offset;
		document.getElementById('aDashboardDataFilterPagination').style.display = 'inline-block';
	} else {
		elOffset.innerText = 1;
		document.getElementById('aDashboardDataFilterPagination').style.display = 'none';
	}
	if( pagination.offsetPlusLimit > pagination.total ) {
		elOffsetPlusLimit.innerText = pagination.total;
	} else {
		elOffsetPlusLimit.innerText = pagination.offsetPlusLimit;
	}
	elTotal.innerText = pagination.total;
}


function aDashboardAppendDataFilters( filters, url, cb, afterSave ) {

	let elFilterInputs = document.getElementById('aDashboardDataFilterInputs');
	while( elFilterInputs.hasChildNodes() ) { 		// Deleting previousely created child nodes...
		elFilterInputs.removeChild( elFilterInputs.lastChild );
	}

	let empty = true;
	for( key in filters ) {
		it = filters[key];
		let type = ( 'type' in it ) ? it.type : 'text';
		let title = ( 'title' in it ) ? it.title : key;
		if( type === 'text' || type === 'number' ) {
			let el = document.createElement("input");
			if( type === 'text' ) {
				el.setAttribute('type', 'text' );
				el.setAttribute('size', '4' );
			} else {
				el.setAttribute('type', 'number' );
				el.style.width = '82px';
				el.style.fontSize = '12px';
				el.style.fontWeight = 'normal';
			}
			el.setAttribute('data-filter', key );
			el.setAttribute('label', title );
			el.setAttribute('id', 'aDashboardFilter_'+key );
			el.style.margin= "0px 8px 0px 4px";
			el.style.border = '1px solid #afafaf';
			el.style.borderRadius = '4px';
			el.style.fontWeight = 'normal';
			el.className= "a-dashboard-filter-input";
			el.onclick = function() { aDashboardDisplayPaginationElements(false) };

			let elLabel = document.createElement("label");
			elLabel.htmlFor = 'aDashboardFilter_'+key;
            elLabel.innerHTML = (title in _aDashboardFieldTitles) ? (_aDashboardFieldTitles[title]) : title;
			elLabel.style.fontWeight = 'normal';
			el.style.margin= "0px 14px 0px 4px";

			elFilterInputs.appendChild(elLabel);
			elFilterInputs.appendChild(el);

			empty = false;
		}
	}

	let elFilterButton = document.getElementById('aDashboardDataFilterButton');
	if( afterSave !== null ) {
		elFilterButton.onclick = function() { aRequestDashboardDataArray(url, cb, afterSave, null); };
	} else {
		elFilterButton.onclick = null;
	}

	document.getElementById('aDashboardFilterOffset').innerText = 1; 	// 

	let elFilters = document.getElementById('aDashboardDataFilterElements');
	elFilters.style.display = ( !empty ) ? 'inline-block' : 'none';
}


function aDashboardDisplayPaginationElements( on ) {
	document.getElementById('aDashboardDataFilterPagination').style.display = (on) ? 'inline' : 'none';
}


function aDashboardFilterPage( dir ) {	
	let filterButton = document.getElementById('aDashboardDataFilterButton');
	if( !filterButton.onclick )
		return;

	let elOffset = document.getElementById('aDashboardFilterOffset');
	let offset = parseInt( elOffset.innerText );
	let elOffsetPlusLimit = document.getElementById('aDashboardFilterOffsetPlusLimit');
	let offsetPlusLimit = parseInt( elOffsetPlusLimit.innerText );
	let elTotal = document.getElementById('aDashboardFilterTotal');
	let total = parseInt( elTotal.innerText );

	if( dir == 1 ) {
		offset += _aDashboardDataPageSize;
		offsetPlusLimit += _aDashboardDataPageSize;
		if( offset > total ) {
			return;
		}
		if( offsetPlusLimit > total ) {	
			offsetPlusLimit = total;
		}
	} else {
		offset -= _aDashboardDataPageSize;
		offsetPlusLimit -= _aDashboardDataPageSize;
		if( offsetPlusLimit < 1 ) {	
			return;
		}
		if( offsetPlusLimit < offset + _aDashboardDataPageSize ) {	
			offsetPlusLimit = offset + _aDashboardDataPageSize - 1;
			if( offsetPlusLimit > total ) {
				offsetPlusLimit = total;
			}
		}
		if( offset < 1 ) {
			offset = 1;
		}
	}

	elOffset.innerText= offset;
	elOffsetPlusLimit.innerText = offsetPlusLimit;

	filterButton.onclick();
}


function aDashboardAppendDataRequestWithFilters( request ) {
	let filters = document.querySelectorAll('[data-filter]');
	//console.log( filters );
	let queryString = '';
	let offset = 0;
	for( let i = 0 ; i < filters.length ; i++ ) {
		let filterName = filters[i].getAttribute('data-filter');
		let filterValue='';
		if( filterName == 'offset' ) {
			filterValue = document.getElementById('aDashboardFilterOffset').innerText;
			offset = filterValue;
		} else if( filterName == 'offsetPlusLimit' ) {
			continue; //filterValue = _aDashboardDataPageSize; //document.getElementById('aDashboardFilterOffsetPlusLimit').innerText;
		} else {
			filterValue = filters[i].value;
		}
		if( !filterValue )
			continue;
		if( !(filterValue.length > 0) )
			continue;

		if( queryString.length > 0 ) {
			queryString += '&';
		}
		queryString += encodeURIComponent( filterName ) + '=' + encodeURIComponent( filterValue );
	}
	if( queryString.length > 0 ) {
		request += '?' + queryString + "&" + "offsetPlusLimit=" + (parseInt(offset) + parseInt(_aDashboardDataPageSize) - 1);
	}
	//console.log(request);
	return request;
}
