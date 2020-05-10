
var NS = "http://www.w3.org/2000/svg";
var XLINK ="http://www.w3.org/1999/xlink";
var _gantt;
var _ganttSettings = { leftChartMargin:0, rightChartMargin:0, minFontSize:6, maxFontSize:18,
	actualDateLineColor:'#af7f7f', caColor:'#c0f0c0',  caTextColor:'#2f4f2f', opColor:'#2f4f4f', opTextColor:'#ffffff',
	haColor:'#a0c0a0', suColor:'#407040', suColor2:'#202020', 
	prognosedColor:'#ff7070', actualColor:'#0f2f0f',
	xAxisBkgrColor:'#e7f0e7', yAxisOddColor:'#efffef', yAxisEvenColor:'#e7f0e7' };

var _ganttActualDate = null;

var _ganttMinDateS = null;
var _ganttMaxDateS = null;
var _ganttDateRangeS = null

var	_ganttXSVGLeft, _ganttXSVGTop, _ganttXSVGWidth, _ganttXSVGHeight, 
	_ganttYSVGLeft, _ganttYSVGTop, _ganttYSVGWidth, _ganttYSVGHeight,
 	_ganttSVGLeft, _ganttSVGTop, _ganttSVGWidth, _ganttSVGHeight;

var _ganttCalculateFarmSquares = false;
var _ganttOverallArea = null;

var _ganttDiv = null;
var _ganttChartDiv, _ganttXDiv, _ganttYDiv;

var _ganttChartSVG = null;
var _ganttXSVG = null;
var _ganttYSVG = null;

var _ganttTooltipDiv = null;
var _ganttTooltipRelatedTarget = null;

function ganttTooltip(e, el, html) {
	if( _ganttTooltipDiv === null ) {
		_ganttTooltipDiv = document.createElement('div');
		_ganttTooltipDiv.style.position = 'absolute';
		_ganttTooltipDiv.style.width = '200px';
		_ganttTooltipDiv.style.height = '100px';
		_ganttTooltipDiv.style.borderRadius = '8px';
		_ganttTooltipDiv.style.border = '1px dotted white';
		_ganttTooltipDiv.style.padding = '2px';
		_ganttTooltipDiv.style.fontSize = '12px';
		_ganttTooltipDiv.style.color = '#dfdfdf';
		_ganttTooltipDiv.style.backgroundColor = '#2f4f2f';
		document.body.appendChild( _ganttTooltipDiv );
		_ganttTooltipDiv.style.display = 'none';
		_ganttTooltipRelatedTarget = null;
	}
	if( _ganttTooltipDiv.style.display === 'none' || _ganttTooltipRelatedTarget != el ) {
		_ganttTooltipDiv.style.left = e.pageX+'px';
		_ganttTooltipDiv.style.top = e.pageY+'px';
		_ganttTooltipDiv.innerHTML = html;
		_ganttTooltipDiv.style.display = 'block';
		_ganttTooltipRelatedTarget = el;
	} else {
		_ganttTooltipDiv.style.display = 'none';
		_ganttTooltipRelatedTarget = null;
	}
}


function ganttInit(ganttDiv, data, actualDate = null) {
	_ganttDiv = ganttDiv;
	_gantt = data;
	//_ganttActualDate = calendarStringToDate( '2020-03-20' ).getTime()/1000;  // 
	_ganttActualDate = (actualDate === null ) ? Date.now() / 1000 : actualDate;

	_ganttDivWidth = ganttDiv.offsetWidth-20; //window.innerWidth-20;
	_ganttDivHeight = Math.floor( window.innerHeight*0.75 );

	//let ganttDiv = document.getElementById('gantt');
	ganttDiv.style.left = '0px';	
	ganttDiv.style.top = '0px';
	ganttDiv.style.width = _ganttDivWidth.toString() + 'px';
	ganttDiv.style.height = _ganttDivHeight.toString() + 'px';

	_ganttXSVGLeft = Math.floor(_ganttDivWidth*0.15);
	_ganttXSVGTop = 0;
	_ganttXSVGWidth = Math.floor(_ganttDivWidth*0.85);
	_ganttXSVGHeight = Math.floor(_ganttDivHeight*0.1);

	_ganttYSVGLeft = 0;
	_ganttYSVGTop = Math.floor(_ganttDivHeight*0.1);
	_ganttYSVGWidth = Math.floor(_ganttDivWidth*0.15);
	_ganttYSVGHeight = Math.floor(_ganttDivHeight*0.9);

	_ganttChartSVGLeft = Math.floor(_ganttDivWidth*0.15);
	_ganttChartSVGTop = Math.floor(_ganttDivHeight*0.1);
	_ganttChartSVGWidth = Math.floor(_ganttDivWidth*0.85);
	_ganttChartSVGHeight = Math.floor(_ganttDivHeight*0.9);

	let ganttChartDiv = document.getElementById('ganttChart');
	ganttChartDiv.style.left = _ganttChartSVGLeft.toString() + 'px';	
	ganttChartDiv.style.top = _ganttChartSVGTop.toString() + 'px';	
	ganttChartDiv.style.width = (_ganttChartSVGWidth).toString() + 'px';
	ganttChartDiv.style.height = (_ganttChartSVGHeight+8).toString() + 'px';

    _ganttChartSVG = svgCreateContainer( 0, 0, _ganttChartSVGWidth, _ganttChartSVGHeight, {} );
	ganttChartDiv.appendChild( _ganttChartSVG );

	let ganttYDiv = document.getElementById('ganttY');
	ganttYDiv.style.left = _ganttYSVGLeft.toString() + 'px';	
	ganttYDiv.style.top = _ganttYSVGTop.toString() + 'px';	
	ganttYDiv.style.width = _ganttYSVGWidth.toString() + 'px';
	ganttYDiv.style.height = _ganttYSVGHeight.toString() + 'px';

    _ganttYSVG = svgCreateContainer( 0, 0, _ganttYSVGWidth, _ganttYSVGHeight, {} );
	ganttYDiv.appendChild( _ganttYSVG );

	let ganttXDiv = document.getElementById('ganttX');
	ganttXDiv.style.left = _ganttXSVGLeft.toString() + 'px';	
	ganttXDiv.style.top = _ganttXSVGTop.toString() + 'px';	
	ganttXDiv.style.width = _ganttXSVGWidth.toString() + 'px';
	ganttXDiv.style.height = _ganttXSVGHeight.toString() + 'px';

    _ganttXSVG = svgCreateContainer( 0, 0, _ganttXSVGWidth, _ganttXSVGHeight, {} );
	ganttXDiv.appendChild( _ganttXSVG );

	for( let f = 0 ; f < _gantt.farms.length ; f++ ) {
		_gantt.farms[f].square = 0;
	}
	for( let i = 0 ; i < _gantt.cultivation_assignments.length ; i++ ) {
		if( !_ganttCalculateFarmSquares ) { 	//	If no calculation of squares is required, making em all equal "1"
			_gantt.cultivation_assignments[i].square = 1;
		}
		for( let f = 0 ; f < _gantt.farms.length ; f++ ) {		
			if( _gantt.cultivation_assignments[i].farm_id == _gantt.farms[f].id ) {
				_gantt.farms[f].square += _gantt.cultivation_assignments[i].square;
				break;					
			}
		}
	}

	_ganttOverallArea = 0;
	for( let f = 0 ; f < _gantt.farms.length ; f++ ) {
		_gantt.farms[f].y = _ganttOverallArea;
		_ganttOverallArea += _gantt.farms[f].square;
	}

	_ganttMinDateS = null;
	_ganttMaxDateS = null;
	for( let i = 0 ; i < _gantt.cultivation_assignments.length ; i++ ) {
		ca = _gantt.cultivation_assignments[i];
		ganttInitDates( ca );
		ganttConfirmValidValue( ca, 'amount_by_plan' );
		ganttConfirmValidValue( ca, 'amount_prognosed' );
		ganttConfirmValidValue( ca, 'amount_actual' );
		if( 'operations' in ca ) {
			let ops = ca.operations;
			for( o = 0 ; o < ops.length ; o++ ) {
				ganttInitDates( ops[o] );
			}
		}
	}
	for( let i = 0 ; i < _gantt.harvesting_assignments.length ; i++ ) {
		ha = _gantt.harvesting_assignments[i];
		ganttInitDates( ha );
	}
	for( let i = 0 ; i < _gantt.supplies.length ; i++ ) {
		su = _gantt.supplies[i];
		ganttInitDates( su, 'su' );
	}                           
	let dateMargin = (_ganttMaxDateS - _ganttMinDateS)/10; 
	_ganttMinDateS = _ganttMinDateS - dateMargin;
	_ganttMaxDateS = _ganttMaxDateS + dateMargin;
	_ganttDateRangeS = _ganttMaxDateS - _ganttMinDateS + 1;

	svgCreateDefs(_ganttChartSVG);

	ganttDrawY();
	ganttDrawChart();
	ganttDrawX();
	ganttDrawActualDate();
}


function ganttInitDates( elem, type=null ) {
	if( type === 'su' ) {
		elem.start_by_plan_date = calendarStringToDate( elem.deliver_to );
		elem.finish_by_plan_date = calendarStringToDate( elem.deliver_to );
		elem.start_by_plan_s = elem.start_by_plan_date.getTime() / 1000;
		elem.finish_by_plan_s = elem.finish_by_plan_date.getTime() / 1000;
		elem.start_prognosed_s = elem.start_by_plan_s;
		elem.finish_prognosed_s = elem.finish_actual_s;
		elem.start_actual_s = elem.start_by_plan_s;
		elem.finish_actual_s = elem.finish_actual_s;
		_ganttMinDateS = Math.min( _ganttMinDateS, elem.start_by_plan_s );
		_ganttMaxDateS = Math.max( _ganttMaxDateS, elem.finish_by_plan_s );
		return;
	} 
	elem.start_by_plan_date = calendarStringToDate( elem.start_by_plan );
	elem.finish_by_plan_date = calendarStringToDate( elem.finish_by_plan );
	elem.start_by_plan_s = elem.start_by_plan_date.getTime() / 1000;
	elem.finish_by_plan_s = elem.finish_by_plan_date.getTime() / 1000;
	
	let start_prognosed = ( elem.start_prognosed ) ? elem.start_prognosed : elem.start_by_plan;
	let finish_prognosed = ( elem.finish_prognosed ) ? elem.finish_prognosed : elem.finish_by_plan;

	elem.start_prognosed_date = calendarStringToDate( start_prognosed );
	elem.finish_prognosed_date = calendarStringToDate( finish_prognosed );
	elem.start_prognosed_s = elem.start_prognosed_date.getTime() / 1000;
	elem.finish_prognosed_s = elem.finish_prognosed_date.getTime() / 1000;

	let start_actual = ( elem.start_actual ) ? elem.start_actual : elem.start_by_plan;
	let finish_actual = ( elem.finish_actual ) ? elem.finish_actual : elem.finish_by_plan;
	elem.start_actual_date = calendarStringToDate( start_actual );
	elem.finish_actual_date = calendarStringToDate( finish_actual );
	elem.start_actual_s = elem.start_actual_date.getTime() / 1000;
	elem.finish_actual_s = elem.finish_actual_date.getTime() / 1000;
	if( _ganttMinDateS === null ) {
		_ganttMinDateS = elem.start_by_plan_s;
	}		
	if( _ganttMaxDateS === null ) {
		_ganttMaxDateS = elem.finish_by_plan_s;
	}		

	_ganttMinDateS = Math.min( _ganttMinDateS, elem.start_by_plan_s, elem.start_prognosed_s, elem.start_actual_s );
	_ganttMaxDateS = Math.max( _ganttMaxDateS, elem.finish_by_plan_s, elem.finish_prognosed_s, elem.finish_actual_s );
}


function ganttDrawX() {
	ganttDrawTimeScale( _ganttXSVG, _ganttXSVGWidth, _ganttXSVGHeight, _ganttMinDateS, _ganttDateRangeS );
}

function ganttDrawY() {

	for( let f = 0 ; f < _gantt.farms.length ; f++ ) {
		let farm = _gantt.farms[f];
		let x1 = 0
		let x2 = _ganttYSVGWidth;
		let y1 = ganttYToScreen(farm.y);
		let y2 = (f < _gantt.farms.length-1) ? ganttYToScreen( _gantt.farms[f+1].y ) : _ganttYSVGHeight;
		let fill = ((f+1)%2 === 0) ? _ganttSettings.yAxisEvenColor : _ganttSettings.yAxisOddColor;
		let rect = svgCreateRect( x1, y1, (x2-x1), (y2-y1), { fill:fill} );
		_ganttYSVG.appendChild( rect );

		let text = svgCreateText( farm.title, x1+10, y1+(y2-y1)/2, { textAnchor:'start', alignmentBaseline:'middle' } );
		_ganttYSVG.appendChild( text );
	}
}


function ganttDrawActualDate() {
	let x1 = ganttTimeToScreen( _ganttActualDate );
	let x2 = x1;
	let y1 = ganttYToScreen(0);
	let y2 = ganttYToScreen(_ganttOverallArea);
	let color = _ganttSettings.actualDateLineColor;
	let rect = svgCreateLine( x1, y1, x2, y2, { stroke:color} );
	_ganttChartSVG.appendChild( rect );
}


function ganttDrawChart() {
	for( let f = 0 ; f < _gantt.farms.length ; f++ ) {
		let farm = _gantt.farms[f];
		let farmY1 = ganttYToScreen(farm.y);
		let farmY2 = (f < _gantt.farms.length-1) ? ganttYToScreen( _gantt.farms[f+1].y ) : _ganttYSVGHeight;
		let farmFill = ((f+1)%2 === 0) ? _ganttSettings.yAxisEvenColor : _ganttSettings.yAxisOddColor;
		let farmRect = svgCreateRect( 0, farmY1, _ganttChartSVGWidth, (farmY2-farmY1), { fill:farmFill} );
		_ganttChartSVG.appendChild( farmRect );

		let ca = _gantt.cultivation_assignments;
		let yOffset = 0;
		for( let c = 0 ; c < ca.length ; c++ ) {
			if( ca[c].farm_id != farm.id ) {
				continue;
			}
			caSquare = parseInt(ca[c].square);
			let caRect = ganttDrawChartRect(ca[c], farm.y, yOffset, caSquare, 'ca' );

			if( 'operations' in ca[c] && false ) { // "&& false" is added to hide operations. May be uncommented...
				let ops = ca[c].operations;
				for( o = 0 ; o < ops.length ; o++ ) {
					ganttDrawChartRect(ops[o], farm.y, yOffset, caSquare, 'op', ops[o].id );
				}
			}
			
			let ha = _gantt.harvesting_assignments;
			for( let h=0 ; h < ha.length ; h++ ) {
				if( ha[h].cultivation_assignment_id != ca[c].id ) {
					continue;
				}
				ganttDrawChartRect(ha[h], farm.y, yOffset, caSquare, 'ha' );

				let su = _gantt.supplies;
				for( let s=0 ; s < su.length ; s++ ) {
					if( su[s].id != ha[h].supply_id ) {
						continue;
					}
					ganttDrawChartRect(su[s], farm.y, yOffset, caSquare, 'su' );
				}
			}

			yOffset += caSquare;
		}
	}
} 


function ganttDrawChartRect( el, y, yOffset, square, type, subType=null ) {
	let x1 = ganttTimeToScreen( el.start_by_plan_s );
	let x2 = ganttTimeToScreen( el.finish_by_plan_s );
	let w = ganttDrawRectWidth(x1,x2);
	let y1 = ganttYToScreen( y + yOffset );
	let y2 = ganttYToScreen( y + yOffset + square );
	//console.log(`square=${square}, y1=${y1}, y2=${y2}`);
	let fill = ganttDrawRectColor(type,'by_plan', subType);
	let opacity = (type == 'op') ? 0.1 : 1.0;
	let rect = svgCreateRect( x1, y1+2, w, (y2-y1)-4, { fill:fill, opacity:opacity } );
	rect.style.cursor = 'pointer';
	_ganttChartSVG.appendChild( rect );
	if( type == 'ca' ) { 	// Drawing text
		let crop_title = '';
		for( let i = 0 ; i < _gantt.crops.length ; i++ ) {
			if( el.crop_id == _gantt.crops[i].id ) { 
				crop_title = _gantt.crops[i].title;
				break;
			}
		}

		let crop = `<a href='/crops/${el.crop_id}' target="_blank" style='color:white;'>${crop_title}</a>`;
		let tooptipHTML = `<div align=center>Cultivation:<br/><b>${el.title}</b></div>${crop}<br/>amount: ${el.amount_by_plan}`;
		rect.onclick = function(e) { ganttTooltip( e, this, tooptipHTML ); };
		//rect.onmouseout = function(e) { ganttTooltipOff(); };

		let fontSize = Math.ceil((y2-y1) *0.75);
		if( fontSize > _ganttSettings.maxFontSize ) {
			fontSize = _ganttSettings.maxFontSize;
		}
		if( fontSize > _ganttSettings.minFontSize ) {
			let text = svgCreateText( el.title, x1+10, y1+(y2-y1)/2+2, 
				{ fontSize:fontSize, fill:_ganttSettings.caTextColor, textAnchor:'start', alignmentBaseline:'middle' } );
			_ganttChartSVG.appendChild( text );
		}
	}
	if( type == 'op' ) { 	// Drawing text
		let tooptipHTML = `<div align=center>Operation:<br/><b>${el.title}</b></div>`;
		rect.onclick = function(e) { ganttTooltip( e, this, tooptipHTML ); };
	}

	if( type == 'ha' ) {
		let supply = `<a href='/supply/${el.supply_id}' style='color:white;' target="_blank">Go to Supply-></a>`;
		let tooptipHTML = `<div align=center>Harvesting:<br/><b>${el.title}</b></div>${supply}`;
		rect.onclick = function(e) { ganttTooltip( e, this, tooptipHTML ); };
		let hElem = document.createElementNS(NS, 'use');
		hElem.setAttributeNS(XLINK, 'xlink:href', '#svgHarvestingIcon');
		hElem.setAttributeNS(null,'x',x2-24);
		hElem.setAttributeNS(null,'y',y2);
		_ganttChartSVG.appendChild( hElem );
	}

	if( type == 'su' ) {
		let tooptipHTML = `<div align=center>Supply</div>`;
		rect.onclick = function(e) { ganttTooltip( e, this, tooptipHTML ); };
		let rhomb = svgCreateRhomb( x1+1, y1+(y2-y1)*0.5-2, 4, { fill: _ganttSettings.suColor2 } );
		_ganttChartSVG.appendChild( rhomb );
		rhomb.onclick = function(e) { ganttTooltip( e, this, tooptipHTML ); };

		let sElem = document.createElementNS(NS, 'use');
		sElem.setAttributeNS(XLINK, 'xlink:href', '#svgSupplyIcon');
		sElem.setAttributeNS(null,'x',x2+4);
		sElem.setAttributeNS(null,'y',y2);
		_ganttChartSVG.appendChild( sElem );
	}

	let start_s = null;
	if( el.start_actual ) {
		start_s = el.start_actual_s;
	} else if( el.start_prognosed ) {
		start_s = el.start_prognosed_s;
	}
	let finish_s = null;
	if( el.finish_actual ) {
		finish_s = el.finish_actual_s;
	} else if( el.finish_prognosed ) {
		finish_s = el.finish_prognosed_s;
	} else {
		finish_s = el.finish_by_plan_s;
	} 

	if( start_s !== null && finish_s !== null ) {
		x1 = ganttTimeToScreen( start_s );
		x2 = ganttTimeToScreen( finish_s );
		w = ganttDrawRectWidth(x1,x2)
		y1 = ganttYToScreen( y + yOffset ) + 1;
		y2 = y1+4;
		fill = ganttDrawRectColor(type,'actual');
		rect = svgCreateRect( x1, y1+1, w, (y2-y1), { fill:fill } );
		_ganttChartSVG.appendChild( rect );
	}
	return rect;
}


function ganttDrawRectWidth(x1,x2) {
	let w = x2-x1+1;
	if( w < 2 ) {
		w = 2;
	}
	return w;	
}


function ganttDrawRectColor(type,rectType,subType=null) {
	let fill='white';
	if( rectType == 'by_plan' ) {
		if( type == 'ca' ) {
			fill = _ganttSettings.caColor;
		} else if ( type == 'op' ) {
			fill = _ganttSettings.opColor;
		}else if ( type == 'ha' ) {
			fill = _ganttSettings.haColor;
		} else if( type == 'su' ) {
			fill = _ganttSettings.suColor;
		}
	} else if( rectType == 'prognosed' ) {
		fill = _ganttSettings.prognosedColor;
	} else if( rectType == 'actual' ) {
		fill = _ganttSettings.actualColor;
	}		
	return fill;
}


function ganttTimeToScreen( timeInSeconds ) {
	let availableSVGWidth = _ganttChartSVGWidth - _ganttSettings.leftChartMargin - _ganttSettings.rightChartMargin;
	return parseInt( _ganttSettings.leftChartMargin + (timeInSeconds - _ganttMinDateS) * availableSVGWidth / _ganttDateRangeS + 0.5); 
}


function ganttTimeToScreenInt( timeInSeconds ) {
	let x = timeToScreen(timeInSeconds);
	return parseInt(x+0.5); 
}

function ganttYToScreen( y ) {
	return Math.floor( y * (_ganttChartSVGHeight-8) / _ganttOverallArea ); 
} 


function ganttConfirmValidValue( obj, key, defaultValue='?' ) {
	if( !(key in obj) ) {
		obj[key] = defaultValue;
	} else if( obj[key] === null ) {
		obj[key] = defaultValue;
	} else if( obj[key].length === 0 ) {
		obj[key] = defaultValue;
	}
}
