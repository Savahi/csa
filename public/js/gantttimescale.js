
var _ganttTimeScale=null;
var _ganttTimeScaleWidth=0;
var _ganttTimeScaleHeight=0;

var _ganttTimeScaleBkgr=null;

var _ganttTimeScaleSettings = { minRectWidthOnTimeScale:20, timeScaleMaxFontSize:16 };

var _ganttTimeScaleGrid;

function ganttDrawTimeScale( svgTimeScale, svgWidth, svgHeight, timeMin, timeWidth ) {
	_ganttTimeScale = svgTimeScale;
	_ganttTimeScaleWidth = svgWidth;
	_ganttTimeScaleHeight = svgHeight;
    _ganttTimeScaleGrid = [];

	while (_ganttTimeScale.hasChildNodes()) {
		_ganttTimeScale.removeChild(_ganttTimeScale.lastChild);
	}
	_ganttTimeScaleBkgr = svgCreateRect( 0, 0, _ganttTimeScaleWidth, _ganttTimeScaleHeight, { fill:_ganttSettings.xAxisBkgrColor } ); 	// backgroud rect
	_ganttTimeScale.appendChild( _ganttTimeScaleBkgr );			

	let displayHours=0, displayDays=false, displayWeeks=false, displayMonths=0, displayYears=0;
	let hourRectWidth, dayRectWidth, weekRectWidth, monthRectWidth, yearRectWidth;
	let hoursInScreen, daysInScreen, weeksInScreen, monthsInScreen, yearsInScreen;
	let fontSize, hoursFontSize, daysFontSize, weeksFontSize, monthsFontSize, yearsFontSize;

	let maxFontSizeAccordingToSVGHeight = _ganttTimeScaleHeight*0.4;

	hoursInScreen = (timeWidth)/ (60*60);
	hourRectWidth = _ganttTimeScaleWidth / hoursInScreen;
	if( hourRectWidth > _ganttTimeScaleSettings.minRectWidthOnTimeScale ) {
		hoursFontSize = hourRectWidth*0.75;
		if( hourRectWidth > _ganttTimeScaleSettings.minRectWidthOnTimeScale*2.5 ) {
			displayHours = 2;
			hoursFontSize = hourRectWidth * 0.75 / 2.5; 
		} else {
			displayHours = 1;
			hoursFontSize = hourRectWidth * 0.75; 				
		}
		if( hoursFontSize > _ganttTimeScaleSettings.timeScaleMaxFontSize ) {
			hoursFontSize = _ganttTimeScaleSettings.timeScaleMaxFontSize;
		}
		if( hoursFontSize > maxFontSizeAccordingToSVGHeight ) {
			hoursFontSize = maxFontSizeAccordingToSVGHeight;
		}
	}

	daysInScreen = hoursInScreen / 24.0;
	dayRectWidth = hourRectWidth * 24.0;
	if( dayRectWidth > _ganttTimeScaleSettings.minRectWidthOnTimeScale ) {
		displayDays = true;		
		daysFontSize = dayRectWidth*0.75;
		if( daysFontSize > _ganttTimeScaleSettings.timeScaleMaxFontSize ) {
			daysFontSize = _ganttTimeScaleSettings.timeScaleMaxFontSize;
		}
		if( daysFontSize > maxFontSizeAccordingToSVGHeight ) {
			daysFontSize = maxFontSizeAccordingToSVGHeight;
		}
	}

	if( !displayDays ) {
		weeksInScreen = daysInScreen / 7.0;
		weekRectWidth = dayRectWidth * 7.0;
		if( weekRectWidth > _ganttTimeScaleSettings.minRectWidthOnTimeScale )	{
			displayWeeks = true;
		}
		weeksFontSize = weekRectWidth*0.75;
		if( weeksFontSize > _ganttTimeScaleSettings.timeScaleMaxFontSize ) {
			weeksFontSize = _ganttTimeScaleSettings.timeScaleMaxFontSize;
		}
		if( weeksFontSize > maxFontSizeAccordingToSVGHeight ) {
			weeksFontSize = maxFontSizeAccordingToSVGHeight;
		}
	}
	if( displayHours == 0 ) {
		monthsInScreen = daysInScreen / 30.0;
		monthRectWidth = dayRectWidth * 30.0;
		if( monthRectWidth > _ganttTimeScaleSettings.minRectWidthOnTimeScale ) {
			if( monthRectWidth > _ganttTimeScaleSettings.minRectWidthOnTimeScale*5 ) {
				displayMonths = 3;
				monthsFontSize = monthRectWidth * 0.75 / 5.0; 
			} else if( monthRectWidth > _ganttTimeScaleSettings.minRectWidthOnTimeScale*1.5 ) {
				displayMonths = 2;
				monthsFontSize = monthRectWidth * 0.75 / 1.5; 
			} else {
				displayMonths = 1;
				monthsFontSize = monthRectWidth * 0.75; 				
			}
			if( monthsFontSize > _ganttTimeScaleSettings.timeScaleMaxFontSize ) {
				monthsFontSize = _ganttTimeScaleSettings.timeScaleMaxFontSize;
			}
			if( monthsFontSize > maxFontSizeAccordingToSVGHeight ) {
				monthsFontSize = maxFontSizeAccordingToSVGHeight;
			}			
		}
	}

	if( !displayDays && displayMonths != 3 ) {
		yearsInScreen = daysInScreen / 365.0;
		yearRectWidth = dayRectWidth * 365.0;
		if( yearRectWidth > _ganttTimeScaleSettings.minRectWidthOnTimeScale ) {
			if( yearRectWidth > _ganttTimeScaleSettings.minRectWidthOnTimeScale * 2 ) {
				displayYears = 2;
				yearsFontSize = yearRectWidth * 0.75 / 2;
			} else {
				displayYears = 1;
				yearsFontSize = yearRectWidth * 0.75;				
			}
			if( yearsFontSize > _ganttTimeScaleSettings.timeScaleMaxFontSize ) {
				yearsFontSize = _ganttTimeScaleSettings.timeScaleMaxFontSize;
			}
			if( yearsFontSize > maxFontSizeAccordingToSVGHeight ) {
				yearsFontSize = maxFontSizeAccordingToSVGHeight;
			}			
		}
	}

	let height = _ganttTimeScaleHeight / 2.0;
	let textProperties = { fill:_ganttTimeScaleSettings.timeScaleFontColor, textAnchor:'middle', alignmentBaseline:'baseline' };
	let rectProperties = { fill:'none', stroke:_ganttTimeScaleSettings.timeScaleStrokeColor, strokeWidth:0.25 };

	//let minTime = _data.visibleMin * 1000; // screenToTime(0) * 1000;
	//let maxTime = _data.visibleMax * 1000; // screenToTime( _ganttTimeScaleWidth ) * 1000;
	let minTime = timeMin * 1000; // screenToTime(0) * 1000;
	let maxTime = minTime + timeWidth * 1000; // screenToTime( _ganttTimeScaleWidth ) * 1000;
	let minDT = new Date(minTime);
	let maxDT = new Date(maxTime);
	let minY = minDT.getFullYear();
	let maxY = maxDT.getFullYear();

	rowNumber = 2;
	if( displayHours != 0 ) {
		textProperties.fontSize = hoursFontSize;
		rectProperties._rowNumber = rowNumber;
		rectProperties._top = (rowNumber - 1) * height;
		rectProperties._height = height;
		drawTimeScaleHours( rectProperties, textProperties, displayHours, minDT, maxDT );
		rowNumber -= 1;
	}

	// Adjusting to the beginning of day
	minDT = new Date( minDT.getFullYear(), minDT.getMonth(), minDT.getDate(), 0, 0, 0, 0 );
	maxDT = new Date( maxDT.getFullYear(), maxDT.getMonth(), maxDT.getDate(), 0, 0, 0, 0 );

	if( displayDays && rowNumber > 0 ) {
		textProperties.fontSize = daysFontSize;
		rectProperties._rowNumber = rowNumber;
		rectProperties._top = (rowNumber - 1) * height;
		rectProperties._height = height;
		drawTimeScaleDays( rectProperties, textProperties, minDT, maxDT  );
		rowNumber -= 1;
	}

	if( displayWeeks && rowNumber > 0 ) {
		textProperties.fontSize = weeksFontSize;
		rectProperties._rowNumber = rowNumber;
		rectProperties._top = (rowNumber - 1) * height;
		rectProperties._height = height;		
		drawTimeScaleWeeks( rectProperties, textProperties, minDT, maxDT );
		rowNumber -= 1;		
	}

	if( displayMonths != 0 && rowNumber > 0 ) {
		textProperties.fontSize = monthsFontSize;
		rectProperties._rowNumber = rowNumber;
		rectProperties._top = (rowNumber - 1) * height;
		rectProperties._height = height;		
		drawTimeScaleMonths( rectProperties, textProperties, displayMonths, minY, maxY, minDT, maxDT );
		rowNumber -= 1;				
	}

	if( displayYears != 0 && rowNumber > 0 ) {
		textProperties.fontSize = yearsFontSize;
		rectProperties._rowNumber = rowNumber;
		rectProperties._top = (rowNumber - 1) * height;
		rectProperties._height = height;		
		drawTimeScaleYears( rectProperties, textProperties, displayYears, minY, maxY );
	}

	// Drawing gantt grid...
	/*
  	for( let i = 0 ;  ; i++ ) {
		let el = document.getElementById( 'ganttBkgrGrid' + i );
		if( !el ) {
			break;
		}
		_ganttSVG.removeChild(el);
	}
	
	let ganttHeight = operToScreen(_data.operations.length);

	let gridLineProperties = { stroke:_ganttTimeScaleSettings.gridColor, strokeWidth:_ganttTimeScaleSettings.gridStrokeWidth, strokeDasharray:_ganttTimeScaleSettings.gridStrokeDashArray }; 
	for( let i = 0 ; i < _timeScaleToGrid.length ; i++ ) {
		let x = timeToScreen( _timeScaleToGrid[i] );
		gridLineProperties.id = 'ganttBkgrGrid' + i;
		let line = createLine( x, 0, x, ganttHeight, gridLineProperties );
		_ganttSVG.appendChild(line);
	}		
	let gridXNow = timeToScreen( _data.proj.curTimeInSeconds );
	gridLineProperties.id = 'ganttBkgrGrid' + _timeScaleToGrid.length;
	gridLineProperties.stroke = _ganttTimeScaleSettings.gridCurrentTimeColor;
	gridLineProperties.strokeDasharray = null; //_ganttTimeScaleSettings.gridStrokeDashArray;
	let gridLine = createLine( gridXNow, 0, gridXNow, ganttHeight, gridLineProperties );
	_ganttSVG.appendChild(gridLine);
	*/
}


function drawTimeScaleYears( rectProperties, textProperties, displayYears, minY, maxY ) {
	let top = rectProperties._top;
	let height = rectProperties._height;
	let bottom = top + height;

	for( let y = minY ; y <= maxY ; y++ ) {
		if( minY == maxY ) {
			let yearText = svgCreateText( minY, _ganttTimeScaleWidth/2, bottom-3, textProperties );
			_ganttTimeScale.appendChild(yearText);
		} else {
			let startOfYear = new Date(y,0,1,0,0,0,0);
			let startOfYearInSeconds = startOfYear.getTime() / 1000;
			let endOfYear = new Date(y,11,31,23,59,59,999);
			let endOfYearInSeconds = endOfYear.getTime() / 1000;
			let yearStartX = ganttTimeToScreen(startOfYearInSeconds, false);
			let yearEndX = ganttTimeToScreen(endOfYearInSeconds, false);
			let yearRect = svgCreateRect( yearStartX, top, yearEndX - yearStartX, height, rectProperties );		
			_ganttTimeScale.appendChild(yearRect);

			let text;
			if( displayYears == 1 ) { // 2-digit format
				text = parseInt(y.toString().slice(-2));
			} else { // 4-digit format
				text = y.toString();
			}
			let yearText = svgCreateText( text, yearStartX + (yearEndX - yearStartX)/2, bottom-3, textProperties );
			_ganttTimeScale.appendChild(yearText);
			//if( rectProperties._rowNumber == 2 ) {
			//	_ganttTimeScaleGrid.push(endOfYearInSeconds); // To draw a grid later on the Gantt chart...
			//}			
		}
	}
}


function drawTimeScaleMonths( rectProperties, textProperties, displayMonths, minY, maxY, minDT, maxDT ) {
	let top = rectProperties._top;
	let height = rectProperties._height;
	let bottom = top + height;

	for( let y = minY ; y <= maxY ; y++ ) {
		let minM = ( y == minY ) ? minDT.getMonth() : 0;
		let maxM = ( y == maxY ) ? maxDT.getMonth() : 11;
		let mNames = _calendarMonthArray; //_texts[_lang]['monthNames']
		for( let m = minM ; m <= maxM ; m++ ) {
			let startOfMonth = new Date(y,m,1,0,0,0,0);
			let startOfMonthInSeconds = startOfMonth.getTime() / 1000;
			let endOfMonth = new Date(y,m+1,0,23,59,59,999);
			let endOfMonthInSeconds = endOfMonth.getTime() / 1000;
			let monthStartX = ganttTimeToScreen(startOfMonthInSeconds, false);
			let monthEndX = ganttTimeToScreen(endOfMonthInSeconds, false);
			let monthRect = svgCreateRect( monthStartX, top, monthEndX - monthStartX, height, rectProperties );		
			_ganttTimeScale.appendChild(monthRect);
			let text;
			if( displayMonths == 3 ) { // Display with year
				yearShort = y.toString().slice(-2);
				text = mNames[m] + "'" + yearShort;
			} else if( displayMonths == 2 ) { // Display with name
				text = mNames[m];
			} else { // Display with digits
				text = (m+1).toString();
			}
			let monthText = svgCreateText( text, monthStartX + (monthEndX - monthStartX)/2, bottom-3, textProperties );
			_ganttTimeScale.appendChild(monthText);
			//if( rectProperties._rowNumber == 2 ) {
			//	_ganttTimeScaleGrid.push(endOfMonthInSeconds); // To draw a grid later on the Gantt chart...
			//}			
		}
	}
}


function drawTimeScaleWeeks( rectProperties, textProperties, minDT, maxDT ) {
	let top = rectProperties._top;
	let height = rectProperties._height;
	let bottom = top + height;
	let numSecondsInDay = 24 * 60 * 60;
	let numSecondsInWeek = 7 * numSecondsInDay;

	let firstDay = minDT.getDay(); // To adjust to the beginning of a week.
	if( firstDay == 0 ) { // If Sunday... 
		firstDay = 7; // ... making it 7 instead of 0
	}
	let startDT;
	if( firstDay > 1 ) { // If not monday...
		startDT = new Date( minDT.getTime() - numSecondsInDay*1000*(firstDay-1) ); // ... making it Monday
	} else {
		startDT = new Date( minDT.getFullYear(), minDT.getMonth(), minDT.getDate(), 0, 0, 0, 0 );
	}

	let startOfWeekInSeconds = startDT.getTime() / 1000;
	let endOfWeekInSeconds = startOfWeekInSeconds + numSecondsInWeek;
	let endInSeconds = maxDT.getTime()/1000 + numSecondsInWeek - 1;		
	for( ; startOfWeekInSeconds < endInSeconds ; ) {
		let weekStartX = ganttTimeToScreen(startOfWeekInSeconds, false);
		let weekEndX = ganttTimeToScreen(endOfWeekInSeconds, false);
		let weekRect = svgCreateRect( weekStartX, top, weekEndX - weekStartX, height, rectProperties );		
		_ganttTimeScale.appendChild(weekRect);
		let startOfWeekDate = new Date( startOfWeekInSeconds*1000 );
		let weekText = svgCreateText( (startOfWeekDate.getDate()).toString(), 
			weekStartX + (weekEndX - weekStartX)/2, bottom-3, textProperties );
		_ganttTimeScale.appendChild(weekText);
		//if( rectProperties._rowNumber == 2 ) {
		//	_ganttTimeScaleGrid.push(endOfWeekInSeconds); // To draw a grid later on the Gantt chart...
		//}
		startOfWeekInSeconds = endOfWeekInSeconds;
		endOfWeekInSeconds += numSecondsInWeek;
	}								
}


function drawTimeScaleDays( rectProperties, textProperties, minDT, maxDT ) {
	let top = rectProperties._top;
	let height = rectProperties._height;
	let bottom = top + height;
	let numSecondsInDay = 24 * 60 * 60;

	let startOfDayInSeconds = minDT.getTime() / 1000;
	let endOfDayInSeconds = startOfDayInSeconds + numSecondsInDay;
	let endInSeconds = maxDT.getTime()/1000 + 1;		
	for( ; startOfDayInSeconds < endInSeconds ; ) {
		let dayStartX = ganttTimeToScreen(startOfDayInSeconds, false);
		let dayEndX = ganttTimeToScreen(endOfDayInSeconds, false);
		let dayRect = svgCreateRect( dayStartX, top, dayEndX - dayStartX, height, rectProperties );		
		_ganttTimeScale.appendChild(dayRect);
		let startOfDayDate = new Date( startOfDayInSeconds*1000 );
		let dayText = svgCreateText( (startOfDayDate.getDate()).toString(), 
			dayStartX + (dayEndX - dayStartX)/2, bottom-3, textProperties );
		_ganttTimeScale.appendChild(dayText);
		//if( rectProperties._rowNumber == 2 ) {
		//	_ganttTimeScaleGrid.push(endOfDayInSeconds); // To draw a grid later on the Gantt chart...
		//}		
		startOfDayInSeconds = endOfDayInSeconds;
		endOfDayInSeconds += numSecondsInDay;
	}								
}


function drawTimeScaleHours( rectProperties, textProperties, displayHours, minDT, maxDT ) {
	let top = rectProperties._top;
	let height = rectProperties._height;
	let bottom = top + height;
	let numSecondsInHour = 60 * 60;

	let startDT = new Date( minDT.getFullYear(), minDT.getMonth(), minDT.getDate(), minDT.getHours(), 0, 0, 0 );
	let endDT = new Date( maxDT.getFullYear(), maxDT.getMonth(), maxDT.getDate(), maxDT.getHours(), 0, 0, 0 );

	let currentHour = startDT.getHours();
	let startOfHourInSeconds = startDT.getTime() / 1000;
	let endOfHourInSeconds = startOfHourInSeconds + numSecondsInHour;
	let endInSeconds = endDT.getTime()/1000 + 1;		
	for( ; startOfHourInSeconds < endInSeconds ; ) {
		let hourStartX = ganttTimeToScreen(startOfHourInSeconds, false);
		let hourEndX = ganttTimeToScreen(endOfHourInSeconds, false);
		let hourRect = svgCreateRect( hourStartX, top, hourEndX - hourStartX, height, rectProperties );		
		_ganttTimeScale.appendChild(hourRect);

		let text = currentHour.toString();
		if( currentHour < 10 ) {
			text = "0" + text;
		}
		if( displayHours == 2 ) { // Display minutes
			text = text + ":00";			
		}
		let hourText = svgCreateText( text, hourStartX + (hourEndX - hourStartX)/2, bottom-3, textProperties );
		_ganttTimeScale.appendChild(hourText);
		//if( rectProperties._rowNumber == 2 ) {
		//	_ganttTimeScaleGrid.push(endOfHourInSeconds); // To draw a grid later on the Gantt chart...
		//}		
		startOfHourInSeconds = endOfHourInSeconds;
		endOfHourInSeconds += numSecondsInHour;
		currentHour = (currentHour < 23) ? (currentHour+1) : 0;
	}								
}
