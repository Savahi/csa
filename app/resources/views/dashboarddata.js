var _aDashboardDataAfterSaveCallback = null;

var _aDashboardTableRowHeight = 34;

var _aDashboardFieldTitles = { id:'#', 
	title:'<span class="glyphicon glyphicon-comment"></span>', 
	descr:'<span class="glyphicon glyphicon-comment" style="font-size:120%;"></span>', 
	name:'<span class="glyphicon glyphicon-user"></span>', 
	refill:'+<span class="glyphicon glyphicon-usd"></span>',
	position:'<span class="glyphicon glyphicon-star"></span>', 
	balance:_myStrBalance, //'<span class="glyphicon glyphicon-usd"></span>', 
	is_hidden: '<span class="glyphicon glyphicon-eye-close"></span>?', email: '<span class="glyphicon glyphicon-envelope"></span>',
	is_accepted: `<span class="glyphicon glyphicon-eye-open" title='${_myStrAccepted}?'></span>`, 
	is_finished:`<span class="glyphicon glyphicon-thumbs-up" title='${_myStrFinished}?'></span>`, // is_finished: `${_myStrFinished}?`, 
	email: '<span class="glyphicon glyphicon-envelope"></span>',
	contacts:_myStrContacts, 
	icon:'<span class="glyphicon glyphicon-camera"></span>',	
	image_url:'<span class="glyphicon glyphicon-camera"></span>',	
	start_by_plan:`${_myStrStart}(${_myStrPlan})`, finish_by_plan:`${_myStrFinish}(${_myStrPlan})`,
	start_prognosed:`${_myStrStart}(${_myStrPrognose})`, finish_prognosed:`${_myStrFinish}(${_myStrPrognose})`, 
	start_actual:`${_myStrStart}(${_myStrActual})`, finish_actual:`${_myStrFinish}(${_myStrActual})`,
	amount_by_plan:`<span class="glyphicon glyphicon-scale"></span> (${_myStrPlan}, ${_myConstWeightUnit})`, 
	amount_prognosed:`<span class="glyphicon glyphicon-scale"></span> (${_myStrPrognose}, ${_myConstWeightUnit})`, 
	amount_actual:`<span class="glyphicon glyphicon-scale"></span> (${_myStrActual}, ${_myConstWeightUnit})`, 
	amount:`<span class="glyphicon glyphicon-scale"></span>, , ${_myConstWeightUnit}`,
	square:`${_myStrSquare}, ${_myConstSquareUnitHTML}`,
	address:'<span class="glyphicon glyphicon-map-marker"></span>',
	deliver_to:'<span class="glyphicon glyphicon-calendar"></span>',
	made_at:'<span class="glyphicon glyphicon-calendar"></span>',
	price_per_user:'<span class="glyphicon glyphicon-usd"><span style="margin:0px 4px 0px 4px; font-size:120%; font-weight:bold;">/</span><span class="glyphicon glyphicon-user"></span>',
	cultivation_assignment_id:'Cultivation Id', delivery_point_id:'<span class="glyphicon glyphicon-shopping-cart"></span><span class="glyphicon glyphicon-map-marker"></span>' , 
	farm_id:`${_myStrFarm}`, supply_id:`${_myStrSupply}`,
	is_delivered:'<span class="glyphicon glyphicon-thumbs-up"></span>', delivery_info:'<span class="glyphicon glyphicon-info-sign"></span>' }; 


function aDisplayDashboardDataArray( linkElem, dataArray, params, menuElem=null ) {
	if( dataArray === null )
		return;
	if( linkElem ) {
		aDashboardMenuSelect(linkElem, menuElem);
	}

	let adjustCellWidth = function(key, defaultWidth) {
		let w = defaultWidth;
		if( key == 'id' || key == 'user_id' || key == 'farm_id' || key == 'status' ) {
			w = Math.ceil( defaultWidth/4);		
		}	
		return w;
	}

	let dataArrayKeys = []; 	// Keys = sql-table fields
	let checkboxes = []; 		// To store checkbox elements for rows (if specified) and pass it into a function

	// 
	let emptyParams = { tableActions:[], rowActions:[] };		
	if( params === undefined ) {
		params = emptyParams;		
	} else if( params === null ) {
		params = emptyParams;		
	}

	let dataFilterHTML = '';

	// Setting the title
	let dataArrayTitle = ('dataTitle' in params) ? params.dataTitle : linkElem.innerText; 
	document.getElementById('aDashboardDataTitle').innerHTML = dataArrayTitle + dataFilterHTML;

	let numKeys = 0;
	if( dataArray.length > 0 ) {
		for( let key in dataArray[0] ) {
			numKeys+=1;
		}
	}

	// Calculating dimensions of table/columns/rows/cells
	let bbox = _aDashboardDataArrayDiv.getBoundingClientRect();
	let lefts = [];
	let widths = [];
	let tops = [];
	let heights = [];
	let bw = _aDashboardTableRowHeight-4;
	let defaultRowTitleWidth = Math.max( Math.max( params.tableActions.length*bw-2, params.rowActions.length*bw-2 ), 1 );
	let defaultColumnTitleHeight = window.innerHeight/20;
	if( defaultColumnTitleHeight < _aDashboardTableRowHeight ) {
		defaultColumnTitleHeight = _aDashboardTableRowHeight;
	}
	let defaultHeight = defaultColumnTitleHeight;
	let defaultWidth = Math.max( (bbox.width-defaultRowTitleWidth)/10, (bbox.width-defaultRowTitleWidth)/(numKeys+1) - numKeys*2);
	let keyCounter = 0;
	let left = defaultRowTitleWidth;
	if( numKeys > 0 ) {
	for( let key in dataArray[0] ) {
		if( 'display' in params ) { 	// If displayble fields are specified...
			if( !(key in params.display ) ) { 	// If this one does not belong to these...
				continue;
			}
			if( 'width' in params.display[key] ) {
				let w = params.display[key].width;
				if( typeof(w) === 'string' ) { 	// Number of symbols
					let wMatch = w.match( /^([0-9]+)\%$/ )
					if( wMatch !== null ) {
						w = parseInt(wMatch[1]) * bbox.width / 100;
					}
					else {	
						w = parseInt(w)*12;
					}
				}
				dataArrayKeys.push( key );
				widths.push( w );
				lefts.push( left );
				left += w;
				continue;
			}
		}
		dataArrayKeys.push( key );
		let w = adjustCellWidth(key, defaultWidth);
		widths.push( w );
		lefts.push( left );
		left += w;
		keyCounter++;
	}
	}
	for( let i = 0 ; i < dataArray.length ; i++ ) {
		heights.push( defaultHeight );
		tops.push( defaultColumnTitleHeight + defaultHeight * i ); 
	}

	// To adjust widths if these can not sum up to the width of the container
	let mostRight = lefts[ lefts.length-1 ] + widths[ widths.length-1 ];
	if( mostRight < _aDashboardDataArrayDiv.offsetWidth ) {
		let diff = _aDashboardDataArrayDiv.offsetWidth -  mostRight;
		let leftAccumulativeInc = 0;
		for( let i = 0 ; i < widths.length ; i++ ) {
			let inc = Math.floor( diff * (widths[i] / (mostRight-defaultRowTitleWidth+aDashboardGetScrollbarWidth()+4)) );
			widths[i] += inc;
			lefts[i] += leftAccumulativeInc;
			leftAccumulativeInc += inc;
		}
	}

	// Creating a container ('table') div
	let aTable = document.createElement('div');
	aTable.className = 'a-table';
	//aTable.style.height = window.innerHeight / 2;
	_aDashboardDataArrayDiv.appendChild(aTable);

	if( dataArrayKeys.length == 0 && false ) { 	// No data has been loaded...
		let cell = document.createElement('div');		
		cell.style.left = '0px'; 
		cell.style.top = '0px';
		cell.style.width = bbox.width + 'px'; 
		//cell.style.height = defaultColumnTitleHeight.toString() + 'px';  
		cell.innerHTML = "No data...";
		aTable.appendChild(cell);
		return;	                                                                 	
	}

	aTableRow = document.createElement('div');
	aTable.appendChild(aTableRow);
	aTableRow.className = 'a-table-header';

	// Top Left Corner of the Table
	let cell = document.createElement('div');		
	//cell.style.left = '0px'; 
	//cell.style.top = '0px';
	//cell.style.width = defaultRowTitleWidth.toString() + 'px'; 
	//cell.style.height = defaultColumnTitleHeight.toString() + 'px';  
	//cell.innerHTML = "<span class='glyphicon glyphicon-plus'></span>";
	//cell.className = 'a-table-left-corner';
	aTableRow.appendChild(cell);

	// Top left corner buttons
	for( let itableaction = 0 ; itableaction < params.tableActions.length ; itableaction++ ) {	
		let action = params.tableActions[itableaction];
		let cb = action.cb;
		
		let el = document.createElement('span');
		el.style.fontSize='12px';
		el.style.padding='0px 2px 0px 2px';
		el.title = action.title;
		cell.appendChild(el);
		if( 'glyphIcon' in action ) {
			el.className = action.glyphIcon;
		}
		if( 'innerHTML' in action ) {
			el.innerHTML= action.innerHTML;
		}
		if( typeof(cb) === 'string' ) { 	// If callback is a string - it encodes an action
			if( cb === 'new' ) {
				el.onclick = function(e) { aDisplayDashboardDataArrayEditWindow(dataArrayTitle + ": Create New", dataArrayKeys); };
			}
		} else {
			if( 'cbargs' in action ) {
				el.onclick = function() { cb(dataArray, action.cbargs, checkboxes); };
			} else {
				el.onclick = function() { cb(dataArray, checkboxes); };
			}
		}
	}

	// Column titles...
	for( let k = 0 ; k < dataArrayKeys.length ; k++ ) {
		let cell = document.createElement('div');		
		//cell.style.left = lefts[k].toString() + 'px'; 
		//cell.style.top = '0px';
		//cell.style.width = widths[k].toString() + 'px'; 
		//cell.style.height = defaultColumnTitleHeight.toString() + 'px'; 
		let title = null;
		let tooltip = null;
		if( 'display' in params ) {
			if( 'title' in params.display[dataArrayKeys[k]] ) {
				title = params.display[dataArrayKeys[k]].title;		
			}
			if( 'tooltip' in params.display[dataArrayKeys[k]] ) {
				tooltip = params.display[dataArrayKeys[k]].tooltip;
			}		
		}
		if( title === null && dataArrayKeys[k] in _aDashboardFieldTitles ) {
			title = _aDashboardFieldTitles[dataArrayKeys[k]];
		}
		if( title === null ) {
			title = dataArrayKeys[k];
		}
		cell.innerHTML = title; //dataArrayKeys[k];
		if( tooltip ) {
			cell.title= tooltip; 
		}
		aTableRow.appendChild(cell);
	}


	// Rows
	for( let i = 0 ; i < dataArray.length ; i++ ) {
		aTableRow = document.createElement('div');
		aTable.appendChild(aTableRow);
		aTableRow.className = 'a-table-row';

		// Row title
		let cell = document.createElement('div');		
		//cell.style.left = '0px'; 
		//cell.style.top = '0px'; // tops[i].toString() + 'px';
		cell.style.width = defaultRowTitleWidth.toString() + 'px'; 
		//cell.style.height = heights[i].toString() + 'px'; 
		cell.dataset.index = i;

				for( let irowaction = 0 ; irowaction < params.rowActions.length ; irowaction++ ) {	
					let action = params.rowActions[irowaction];
					let cb = action.cb;
					if( typeof(cb) === 'string' ) { 	// Not a function is specified but an encoded functionality
						if( cb === 'checkbox' ) {
							let el = document.createElement('span');
							el.className = 'glyphicon glyphicon-unchecked';
							el.dataset.checked = 'n';
							el.onclick = function()	{                     
								if (this.dataset.checked === 'n') { this.dataset.checked='y'; this.className='glyphicon glyphicon-check'; } 
								else { this.dataset.checked='n'; this.className='glyphicon glyphicon-unchecked'; }
							};
							el.title = action.title;
							el.dataset.index = i;
							cell.appendChild(el);
							checkboxes.push(el);
						}
					} else {
						let buttonDiv = document.createElement('div');
						//buttonDiv.className = " a-dashboard-action-button";
						if( 'glyphIcon' in action ) {
							let buttonSpan = document.createElement('span');
							buttonSpan.className = action.glyphIcon;
							buttonDiv.appendChild(buttonSpan);
						}
						if( 'innerHTML' in action ) {
							buttonDiv.innerHTML= action.innerHTML;
						}
						buttonDiv.onclick = function() { cb(dataArray[i], buttonDiv); };
						buttonDiv.title = action.title;
						cell.appendChild(buttonDiv);
					}
				}
		cell.className = 'a-table-row-title';
		aTableRow.appendChild(cell);

		// Data
		for( let k = 0 ; k < dataArrayKeys.length ; k++ ) {
			let cell = document.createElement('div');		
			//cell.style.left = lefts[k].toString() + 'px'; 
			//cell.style.top = '0px'; //tops[i].toString() + 'px';
			cell.style.width= widths[k].toString() + 'px'; 
			//cell.style.height= heights[i].toString() + 'px'; 
			let key = dataArrayKeys[k];
			if( 'display' in params ) {
				if( 'cb' in params.display[key] ) { 	// If a rendering call back is specified
					cell.innerHTML='<span class="glyphicon glyphicon-time"></span>';
					params.display[key].cb(dataArray[i], cell);					 
					aTableRow.appendChild(cell);
					continue;
				}
			}
			if( key === 'icon' ) {
				let imageContent = ( dataArray[i][key] ) ? dataArray[i][key] : _aDashboardDataEditWindowIconEmpty; 
				//let imageAttrs = "height=60" + Math.round(defaultHeight*0.8) + " width=" + Math.round(defaultHeight*0.8*4.0/3.0) + " vspace=2 hspace=2";
				let imageAttrs = "style='height:60px; width:80px; border-radius:4px;' vspace=2 hspace=2";
				cell.innerHTML = "<img " + imageAttrs + " src='data:image/jpg;base64, " + imageContent + "'>";
			} else if( key === 'image_url' ) {
				if( dataArray[i][key] ) {
					let imageAttrs = "height=" + Math.round(defaultHeight*0.8) + " width=" + Math.round(defaultHeight*0.8*4.0/3.0) + " vspace=2 hspace=2";
 					cell.innerHTML = `<a href='${dataArray[i][key]}' target=_blank><img ${imageAttrs} src='${dataArray[i][key]}' style='float:left;'></a>`;
				} else {
					cell.innerHTML = '';
				}
				cell.innerHTML += dataArray[i][key];
			}
			else if( key === 'amont' || key === 'balance' || key === 'square' || key === 'cost' || key === 'price' || key === 'price_per_user' ) {
				let innerHTML = (dataArray[i][key] !== null && dataArray[i][key] !== '') ? myFormatNumberBy3(dataArray[i][key]) : '?';
				cell.innerHTML = innerHTML;
				cell.title = innerHTML;   
			} else {
				cell.innerHTML = dataArray[i][key];
				cell.title = dataArray[i][key];   
			}
			//cell.onclick = function(e) { aDisplayDashboardDataArrayEditWindow(dataArrayTitle+": Edit", dataArrayKeys, dataArray[i] ); }
			aTableRow.appendChild(cell);
		}
	}
}


function aDisplayDashboardDataArrayEditWindow( titleText='Untitled', keys, dataItem=null, params=null, cb=null ) {
	let mode = 4;
	if( 'disableSave' in params ) {
		if( params.disableSave ) {
			mode=1;
		}
	} 
    myPopupDivShow( titleText, null, { mode:mode, size:'h' } );

	let rightPaneHTML = null;
	let rightPaneInputs = [];
	let keyProperties={};
	let saveURL='';
	if( params !== null ) {
		if( 'rightPaneHTML' in params ) {
			rightPaneHTML = params.rightPaneHTML;
		}
		if( 'keyProperties' in params ) {
			keyProperties = params.keyProperties;
		}
		if( 'saveURL' in params ) {
			saveURL = params.saveURL;
		}
		if( 'rightPaneInputs' in params ) {
			rightPaneInputs = params.rightPaneInputs;
		}
	}	
	//console.log(JSON.stringify(rightPaneInputs));

	let container = myPopupDivBody(); // document.getElementById('aDashboardDataArrayEditWindowContainer');
	let twoPaneContainer = document.createElement('div');
	twoPaneContainer.className = 'row';
	let leftPane = document.createElement('div');
	twoPaneContainer.appendChild(leftPane);
	if( rightPaneHTML !== null ) {
		let rightPane = document.createElement('div');
		twoPaneContainer.appendChild(rightPane);
		if( typeof(rightPaneHTML) === 'string' ) {
			rightPane.innerHTML = rightPaneHTML;
		} else {
			rightPane.appendChild(rightPaneHTML);
		}
		leftPane.className = 'col-sm-6';
		rightPane.className = 'col-sm-6';
	} else {
		leftPane.className = 'col-sm-12 col-xs-12';
	}
	container.appendChild(twoPaneContainer);			

	if( 'leftPaneHTML' in params ) { 	// An html-code for the left pane...
		let div = document.createElement('div');
		if( typeof(params.leftPaneHTML) === 'string' ) {
			div.innerHTML = params.leftPaneHTML;
		} else {
			div.appendChild(params.leftPaneHTML);
		}
		leftPane.appendChild(div);
	}

	let table = document.createElement('table');
	table.style.width='100%';
	table.style.border='0px';
	leftPane.appendChild(table);

		if( keys === null ) { 		// If "keys" === null, extracting keys from "values"
			keys = [];
			for( let key in dataItem ) {
				keys.push(key);
			}
		}

		let inputs = []	
		for( let k = 0 ; k < keys.length ; k++ ) {
			let props={};
			if( keys[k] in keyProperties ) {
 				props = keyProperties[keys[k]];
			}
			let inputElem;
			if( keys[k] !== 'icon' ) {
				if( dataItem !== null ) {
					inputElem = aCreateDashboardEditWindowControl(keys[k], dataItem[keys[k]], props);
				} else {
					inputElem = aCreateDashboardEditWindowControl(keys[k], '', props);
				}
			} else {
				inputElem = aInitDashboardDataEditWindowIcon(dataItem[keys[k]]);
			}
			inputs.push(inputElem);

			if( !('hidden' in props ) ) {
				let tr =  document.createElement('tr');
				let tdLeft = document.createElement('td');
				let tdRight = document.createElement('td');
				table.appendChild(tr);
				let fieldTitle = ('title' in props) ? props.title : ((keys[k] in _aDashboardFieldTitles) ? _aDashboardFieldTitles[keys[k]] : keys[k]);
				tdLeft.innerHTML = fieldTitle + ":"; // keys[k] + ":";
				tdLeft.style.textAlign='right';
				tdLeft.style.padding='4px 4px 0px 0px';
				tr.appendChild(tdLeft);
				tdRight.style.padding='4px 4px 4px 4px';
				tr.appendChild(tdRight);
				tdRight.appendChild(inputElem);
			} else {
				leftPane.appendChild(inputElem);
			}
		}
	if( cb === null ) {
		myPopupDivSetMode(mode, aDefaultDashboardDataEditWindowSaveFunction, 
			{ keys:keys, inputs:inputs, rightPaneInputs:rightPaneInputs, saveURL:saveURL } );
	}
}

function aHideDashboardDataArrayEditWindow() {
    myPopupDivHide();
	let container =  document.getElementById('aDashboardDataArrayEditWindowContainer');
	while(container.hasChildNodes()) {
		container.removeChild(container.lastChild);	
	}
}


function aSetupDashboardEditWindowList(selectElem, value, dict) {
	let option = document.createElement('option');  
	option.text='...';
	option.value = -1; 
	selectElem.appendChild(option);
	for( let i = 0 ; i < dict.length ; i++ ){
		option = document.createElement('option');  
		option.text = dict[i].title;
		option.value = dict[i].id;
		if( dict[i].id == value ) {
			option.selected = true;
		} 
		selectElem.appendChild(option);
	}
}


function aCreateDashboardEditWindowControl(key, value, props=null) {
	let selectables = { 'crop_id': '/noauth_crops_short', 'farm_id': '/noauth_farms_short', 'farm_admin': '/noauth_farms_short', 
		'delivery_point_id': '/noauth_delivery_points_short', 'delivery_point_admin': '/noauth_delivery_points_short', 
		'delivery_unit_admin': '/noauth_delivery_units_short',  'sorting_station_admin': '/noauth_sorting_stations_short',
		'supply_id': '/noauth_pending_supplies_short'  };
	let selectableNames = { 'crop_id': 'Crops', 'farm_id': 'Farms', 'farm_admin': 'Farms', 
		'delivery_point_id': 'Delivery Points', 'delivery_point_admin': 'Delivery Points', 
		'delivery_unit_admin': 'Delivery Units',  'sorting_station_admin': 'Sorting Stations',
		'supply_id':'Supplies' };

	if( props !== null ) {
		if( 'hidden' in props ) {
			let el = document.createElement('input');
			el.type = 'hidden';
			el.value = value;
			el.name = key;
			return el;
		}
	}
	
	let predefinedType=null;
	if( props !== null ) {
		if( 'type' in props ) {
			predefinedType = props.type;
		}
	}			

	if( selectables.hasOwnProperty( key ) ) { 		// A list of values to read from the DB
		let el = document.createElement('select');
		el.name = key;
		el.style.border='1px solid lightgray';
		el.style.borderRadius = '4px';
		el.style.backgroundColor='#efefef';
		aRequestDataForDashboardEditWindow( selectables[key], selectableNames[key], function(response) {
			aSetupDashboardEditWindowList( el, value, response );
		});
		if( 'readOnly' in props )
			el.disabled = true;
		if( 'required' in props )
			el.__required = props.required;
		if( 'title' in props )
			el.__title = props.title;
		el.__type = 'select';
		return el;
	} else if( key === 'descr'||key === 'text'||key === 'address'||key === 'contacts'|| predefinedType === 'textarea' ) { // A textare should be attached
		let el = document.createElement('textarea');
		el.name = key;
		el.style.border='1px solid lightgray';
		el.style.borderRadius = '4px';
		el.style.backgroundColor='#efefef';
		if( key === 'descr' || key === 'text' || predefinedType === 'textarea' ) {
			el.style.height = ( !('height' in props) ) ? '80px' : props.height;
		} 
		el.style.width = '100%';
		el.value = value;	
		if( 'readOnly' in props )
			el.disabled = true;
		if( 'title' in props )
			el.__title = props.title;
		if( 'required' in props )
			el.__required = props.required;
		return el;
	} else if( key.indexOf('is_') == 0 ) { 		// A checkbox
  		let el = document.createElement("input");
		el.setAttribute("type", "checkbox");
  		el.name = key;
		el.value = value;
		el.checked = (value) ? true : false;
  		el.onclick = function(e) { (el.checked) ? el.value=true : el.value=false; };
		if( 'readOnly' in props ) {
			el.disabled = true;
		} 
		return el;
	} else {
		let el = document.createElement('input');
		el.name = key;
		el.style.border='1px solid lightgray';
		el.style.borderRadius = '4px';
		el.style.backgroundColor='#efefef';
		el.value = value;	
		el.style.width = ( !('width' in props) ) ? '90%' : props.width;
		if( 'readOnly' in props ) {
			el.disabled = true;
		} 
		if( predefinedType )
			el.__type = props.type;
		if( 'min' in props )
			el.__min = props.min;
		if( 'max' in props )
			el.__max = props.max;
		if( 'required' in props )
			el.__required = props.required;
		if( 'title' in props )
			el.__title = props.title;
		return el;
	}
}


function aDefaultDashboardDataEditWindowSaveFunction( params ) {
	let keys = params.keys;
	let inputs = params.inputs;

    let formData = new FormData();
	for( let i = 0 ; i < keys.length ; i++ ) {
		let status = aDashboardEditWindowCheckValidity( inputs[i] );
		if( status.error !== 0 ) {
			//alert( (('__title' in inputs[i]) ? `${inputs[i].__title}: ` : '') +  status.errorMessage );
			let k = keys[i];
			document.getElementById('myPopupDivErrorMessage').innerHTML = 
				(('__title' in inputs[i]) ? `${inputs[i].__title}: ` : ((k in _aDashboardFieldTitles) ? `${_aDashboardFieldTitles[k]}: `:'')) + 
				status.errorMessage;
			//myPopupDivShow( "ERROR", (('__title' in inputs[i]) ? `${inputs[i].__title}: ` : '') +  status.errorMessage, {mode:2} );
			return;
		} 		
		console.log(keys[i], inputs[i].value);
		if( keys[i] === 'icon' ) {
			if( aDashboardDataEditWindowIsIconDeleted() ) {
				formData.append('icon_delete', 'y');
			} else if( aDashboardDataEditWindowIsIconChanged() ) {
				formData.append('icon', aDashboardDataEditWindowIconValue());  
			}
		} else {
	    	formData.append(keys[i], inputs[i].value);  
		}
	}

	if( 'rightPaneInputs' in params ) {
		for( let i = 0 ; i < params.rightPaneInputs.length ; i++ ) {
	    	formData.append(params.rightPaneInputs[i].element.dataset.key, params.rightPaneInputs[i].element.value);  
		}
	}		
	
	let xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	    if (this.readyState == 4 ) {
			alert(this.responseText);
	    	if( this.status == 200 || this.status >= 400 ) {
				if(this.responseText) {
					let r = null;
				    try {
        				r = JSON.parse(this.responseText);
    				} catch(e) {
        				alert( `A error occured while saving data: ${e}` ); // error in the above string (in this case, yes)!
						return;
    				}
					if( !r ) { 
        				alert( `A error occured while saving data` ); // error in the above string (in this case, yes)!
						return;
					}

					if( 'message' in r ) { 		// message exists if it is a db error
						if( r.message.indexOf('token') >= 0 ) {
							alert( "An error occured while saving the data. " + 
								"Possibly your session has been expired or your internet connection is lost. " + 
								"Please, close this page and try to login again.");
						} else {
							alert( "An error occured while saving the data. Possibly the data you have entered are invalid or "+
								"your internet connection is lost, or your session has been expired. " + 
								"Please, try again...");
						}
						//alert(r.message);				
						return;
					}
					if( r.rows_affected == 0 ) {
	        			alert( `An error occured while saving the data: ${r.error_message}` ); // error in the above string (in this case, yes)!
					} else {
						myPopupDivHide();
						// To update the table with the data inserted or updated
						if( _aDashboardDataAfterSaveCallback !== null ) {
							_aDashboardDataAfterSaveCallback.cb( _aDashboardDataAfterSaveCallback.arg1, _aDashboardDataAfterSaveCallback.arg2, r );
						}
						return;
					}
				}
			}
		}
	}

	let metas = document.getElementsByTagName('meta');
	let token=null;
	for (let i = 0 ; i < metas.length; i++) {
    	if( metas[i].getAttribute('name') == 'csrf-token' ) { 
			token = metas[i].getAttribute('content');
		}
 	}

	xmlhttp.open("POST", params.saveURL, true);
	xmlhttp.setRequestHeader("Cache-Control", "no-cache");
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');		
	//xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xmlhttp.setRequestHeader('X-CSRF-TOKEN', token);		
	xmlhttp.send( formData );		
}


function aDashboardEditWindowCheckValidity( el ) { 
	if( '__required' in el ) {
		if( '__type' in el ) { 		
			if( el.__type === 'select' ) { 	// If this one is of type"select", then it must be selected
				if( el.value == -1 ) { 
					return { error:1, errorMessage: _myStrMustBeSelected };					
				}
				return { error:0, errorMessage: '' };					
			}
		}
		emptyOrWhiteSpacesOnly = true;
		for( let i = 0 ; i < el.value.length ; i++ ) {
			if( el.value[i] != ' ' && el.value[i] != '\t' ) {
				emptyOrWhiteSpacesOnly = false;
				break;
			}
		}
		if( el.__required ) {
			if( emptyOrWhiteSpacesOnly ) {
				return { error:1, errorMessage: _myStrMustHaveAValue };
			}
		} else if( emptyOrWhiteSpacesOnly ) {
			el.value = '';
			return { error:0, errorMessage: '' };
		}
	}
 
	if( '__type' in el ) {
		if( el.__type === 'number' ) {
			let f = parseFloat( el.value );
			if( isNaN(f) )
				return { error:1, errorMessage: `Not a number!` };
			
			if( '__min' in el ) {
				if( f < el.__min ) 
					return { error:1, errorMessage: `${_myStrMustBeNotLess}: ${el.__min}` };
			}
			if( '__max' in el ) {
				if( f > el.__max ) 
					return { error:1, errorMessage: `${_myStrMustBeNotMore}: ${el.__max}` };
			}
		} else if( el.__type === 'date' ) {
			let dateReg = /^[ \t]*(\d{4})\-(\d{2})\-(\d{2})/;
			let m = el.value.match(dateReg);
			if( !m )
				return { error:1, errorMessage: `Must be a date: YYYY-MM-DD` };
			if( '__min' in el || '__max' in el ) { 	
				let d = new Date( m[1], m[2]-1, m[3] );
				if( '__min' in el ) {
					let minm = el.__min.match(dateReg);
					if( minm ) {
						let mind = new Date( minm[1], minm[2]-1, minm[3] );
						if( d < mind )
							return { error:1, errorMessage: `Min. date allowed: ${el.__min}` };
					}
				}
				if( '__max' in el ) {
					let maxm = el.__max.match(dateReg);
					if( maxm ) {
						let maxd = new Date( minm[1], minm[2]-1, minm[3] );
						if( d > maxd )
							return { error:1, errorMessage: `Max. date allowed: ${el.__max}` };
					}
				}
			}
		} else if( el.__type === 'datetime' ) {
			let dateReg = /^[ \t]*(\d{4})\-(\d{2})\-(\d{2})[ ]+(\d{2})\:(\d{2})/;
			let m = el.value.match(dateReg);
			if( !m )
				return { error:1, errorMessage: `Must be a datetime: YYYY-MM-DD HH:MM` };
			if( '__min' in el || '__max' in el ) { 	
				let d = new Date( m[1], m[2]-1, m[3], m[4], m[5] );
				if( '__min' in el ) {
					let minm = el.__min.match(dateReg);
					if( minm ) {
						let mind = new Date( minm[1], minm[2]-1, minm[3], minm[4], minm[5] );
						if( d < mind )
							return { error:1, errorMessage: `Min. date allowed: ${el.__min}` };
					}
				}
				if( '__max' in el ) {
					let maxm = el.__max.match(dateReg);
					if( maxm ) {
						let maxd = new Date( minm[1], minm[2]-1, minm[3], maxm[4], maxm[5] );
						if( d > maxd )
							return { error:1, errorMessage: `Min. date allowed: ${el.__max}` };
					}
				}
			}
		}
	}
	return { error:0, errorMessage: '' };
}
