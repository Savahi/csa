
var NS = "http://www.w3.org/2000/svg";

var _gantt;
var _ganttSettings = { leftChartMargin:0, rightChartMargin:0, minFontSize:6, maxFontSize:18, 
	actualDateLineColor:'#af7f7f', caColor:'#c0f0c0',  caTextColor:'#2f4f2f', opColor:'#cfcf9f', opTextColor:'#4f4f4f',
	prognosedColor:'#ff7070', actualColor:'#0f2f0f',
	xAxisBkgrColor:'#e7f0e7', yAxisOddColor:'#e7f0e7', yAxisEvenColor:'white' };

var _ganttActualDate = null;

var _ganttMinDateS = null;
var _ganttMaxDateS = null;
var _ganttDateRangeS = null

var	_ganttXSVGLeft, _ganttXSVGTop, _ganttXSVGWidth, _ganttXSVGHeight, 
	_ganttYSVGLeft, _ganttYSVGTop, _ganttYSVGWidth, _ganttYSVGHeight,
 	_ganttSVGLeft, _ganttSVGTop, _ganttSVGWidth, _ganttSVGHeight;

var _ganttYRange = null;

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


function ganttInit( ganttDiv, data, actualDate = null ) {
	_ganttDiv = ganttDiv;
	_gantt = data;
	_ganttActualDate = calendarStringToDate( '2020-03-20' ).getTime()/1000;  // (actualDate === null ) ? Date.now() / 1000 : actualDate;
	
	_ganttDivWidth = ganttDiv.offsetWidth-20; //window.innerWidth-20;
	_ganttDivHeight = Math.floor( window.innerHeight*0.75 );

	ganttDiv.style.left = '0px';	
	ganttDiv.style.top = '0px';
	ganttDiv.style.width = _ganttDivWidth.toString() + 'px';
	ganttDiv.style.height = _ganttDivHeight.toString() + 'px';

	_ganttXSVGLeft = Math.floor(_ganttDivWidth*0.05);
	_ganttXSVGTop = 0;
	_ganttXSVGWidth = Math.floor(_ganttDivWidth*0.95);
	_ganttXSVGHeight = Math.floor(_ganttDivHeight*0.1);

	_ganttYSVGLeft = 0;
	_ganttYSVGTop = Math.floor(_ganttDivHeight*0.1);
	_ganttYSVGWidth = Math.floor(_ganttDivWidth*0.05);
	_ganttYSVGHeight = Math.floor(_ganttDivHeight*0.9);

	_ganttChartSVGLeft = Math.floor(_ganttDivWidth*0.05);
	_ganttChartSVGTop = Math.floor(_ganttDivHeight*0.1);
	_ganttChartSVGWidth = Math.floor(_ganttDivWidth*0.95);
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

	_ganttYRange = 0;
	for( let c = 0 ; c < _gantt.cultivation_assignments.length ; c++ ) {
		let ca  =_gantt.cultivation_assignments[c];
		ca.y =  _ganttYRange;
		_ganttYRange += 1;
		ganttConfirmValidValue( ca, 'amount_by_plan' );
		ganttConfirmValidValue( ca, 'amount_prognosed' );
		ganttConfirmValidValue( ca, 'amount_actual' );
		if( 'operations' in ca ) {
			ops = ca.operations;
			for( let o = 0 ; o < ops.length ; o++ ) {
				ops[o].y =  _ganttYRange;
				_ganttYRange += 1;
			}
		}
	}

	_ganttMinDateS = null;
	_ganttMaxDateS = null;
	for( let i = 0 ; i < _gantt.cultivation_assignments.length ; i++ ) {
		ca = _gantt.cultivation_assignments[i];
		ganttInitDates( ca );
		if( 'operations' in ca ) {
			let ops = ca.operations;
			for( o = 0 ; o < ops.length ; o++ ) {
				ganttInitDates( ops[o] );
			}
		}
	}
	let dateMargin = (_ganttMaxDateS - _ganttMinDateS)/10; 
	_ganttMinDateS = _ganttMinDateS - dateMargin;
	_ganttMaxDateS = _ganttMaxDateS + dateMargin;
	_ganttDateRangeS = _ganttMaxDateS - _ganttMinDateS + 1;

	ganttDrawY();
	ganttDrawChart();
	ganttDrawX();
	ganttDrawActualDate();
}


function ganttInitDates( elem ) {
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


function ganttDrawActualDate() {
	let x1 = ganttTimeToScreen( _ganttActualDate );
	let x2 = x1;
	let y1 = ganttYToScreen(0);
	let y2 = ganttYToScreen(_ganttYRange);
	let color = _ganttSettings.actualDateLineColor;
	let rect = svgCreateLine( x1, y1, x2, y2, { stroke:color} );
	_ganttChartSVG.appendChild( rect );
}


function ganttDrawX() {
	ganttDrawTimeScale( _ganttXSVG, _ganttXSVGWidth, _ganttXSVGHeight, _ganttMinDateS, _ganttDateRangeS );
}

function ganttDrawY() {

	let cas = _gantt.cultivation_assignments;
	for( let c = 0 ; c < cas.length ; c++ ) {
		let ca = cas[c];
		let x1 = 0
		let x2 = _ganttYSVGWidth;
		let y1 = ganttYToScreen(ca.y);
		let y2 = ganttYToScreen(ca.y+1);
		let fill = _ganttSettings.yAxisEvenColor;
		let rect = svgCreateRect( x1, y1, (x2-x1), (y2-y1), { fill:fill} );
		_ganttYSVG.appendChild( rect );
		if( 'operations' in ca ) {
			ops = ca.operations;
			for( let o = 0 ; o < ops.length ; o++ ) {
				let op =  ops[o];
				let x1 = 0
				let x2 = _ganttYSVGWidth;
				let y1 = ganttYToScreen(ca.y);
				let y2 = ganttYToScreen(ca.y+1);
				let fill = _ganttSettings.yAxisOddColor;
				let rect = svgCreateRect( x1, y1, (x2-x1), (y2-y1), { fill:fill} );
				_ganttYSVG.appendChild( rect );
				//let text = svgCreateText( farm.title, x1+10, y1+(y2-y1)/2, { textAnchor:'start', alignmentBaseline:'middle' } );
				//_ganttYSVG.appendChild( text );
			}
		}						
		//let text = svgCreateText( farm.title, x1+10, y1+(y2-y1)/2, { textAnchor:'start', alignmentBaseline:'middle' } );
		//_ganttYSVG.appendChild( text );
	}
}


function ganttDrawChart() {
	let cas = _gantt.cultivation_assignments;
	for( let c = 0 ; c < cas.length ; c++ ) {
		let ca = cas[c];
		let caRect = ganttDrawChartRect( ca, 'ca' );

		if( 'operations' in ca ) {
			let ops = ca.operations;
			for( o = 0 ; o < ops.length ; o++ ) {
				let op = ops[o];
				ganttDrawChartRect( op, 'op' );
			}
		}
	}
} 


function ganttDrawChartRect( el, type ) {
	let x1 = ganttTimeToScreen( el.start_by_plan_s );
	let x2 = ganttTimeToScreen( el.finish_by_plan_s );
	let w = ganttDrawRectWidth(x1,x2)
	let y1 = ganttYToScreen( el.y );
	let y2 = ganttYToScreen( el.y + 1 );
	let fill = ganttDrawRectColor(type,'by_plan');
	let rect = svgCreateRect( x1, y1+2, w, (y2-y1)-4, { fill:fill } );
	rect.style.cursor = 'pointer';
	_ganttChartSVG.appendChild( rect );

	let fontSize = Math.ceil( (y2-y1) * 0.25 );
	if( fontSize > _ganttSettings.maxFontSize ) {
		fontSize = _ganttSettings.maxFontSize;
	}

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

		if( fontSize > _ganttSettings.minFontSize ) {
			let text = svgCreateText( el.title, x1+10, y1+(y2-y1)/2+2, 
				{ fontSize:fontSize, fill:_ganttSettings.caTextColor, textAnchor:'start', alignmentBaseline:'middle' } );
			_ganttChartSVG.appendChild( text );
		}
	}
	if( type == 'op' ) { 	// Drawing text
		opTitle = el.title;	
		/*
		let opTitle = '';
		for( let i = 0 ; i < _gantt.operations.length ; i++ ) {
			if( el.id == _gantt.operations[i].id ) {
				opTitle = _gantt.operations[i].title;
				break;
			}			
		}
		*/
		let tooptipHTML = `<div align=center>Operation:<br/><b>${opTitle}</b></div>`;
		rect.onclick = function(e) { ganttTooltip( e, this, tooptipHTML ); };
		if( fontSize > _ganttSettings.minFontSize ) {
			let text = svgCreateText( opTitle, x1+10, y1+(y2-y1)/2+2, 
				{ fontSize:fontSize, fill:_ganttSettings.caTextColor, textAnchor:'start', alignmentBaseline:'middle' } );
			_ganttChartSVG.appendChild( text );
		}
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
		finish_S = el.finish_by_plan_s;
	} 

	if( start_s !== null && finish_s !== null ) {
		x1 = ganttTimeToScreen( start_s );
		x2 = ganttTimeToScreen( finish_s );
		w = ganttDrawRectWidth(x1,x2)
		y1 = ganttYToScreen( el.y ) + 1;
		y2 = y1+4;
		fill = ganttDrawRectColor(type,'actual');
		rect = svgCreateRect( x1, y1+1, w, (y2-y1), { fill:fill } );
		_ganttChartSVG.appendChild( rect );
	}
	return rect;
}


function ganttDrawRectWidth(x1,x2) {
	let w = x2-x1;
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
	return Math.floor( y * (_ganttChartSVGHeight-8) / _ganttYRange ); 
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
