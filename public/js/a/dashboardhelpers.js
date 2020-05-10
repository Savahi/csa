
function aDashboardHelperFormatDoneOrNotDoneCell( done ) {
	return (done) ? '<span class="glyphicon glyphicon-ok"></span>':'<span class="glyphicon glyphicon-hourglass"></span>';
}

function aDashboardGetPlanProgActAmountTitle() {
	return `<span class="glyphicon glyphicon-scale"></span> ${_myStrPlan}/${_myStrPrognose}/${_myStrActual}`
} 

function aDashboardGetPlanProgActResAmountTitle() {
	return `<span class="glyphicon glyphicon-scale"></span> ${_myStrPlan}/${_myStrPrognose}/${_myStrActual}/${_myStrReserved}`
} 

function aDashboardGetPlanProgActStartTitle() {
	return `<span class="glyphicon glyphicon-calendar"></span>${_myStrStart}: ${_myStrPlan}/${_myStrPrognose}/${_myStrActual}`;
}

function aDashboardGetPlanProgActFinishTitle() {
	return `<span class="glyphicon glyphicon-calendar"></span> ${_myStrFinish}: ${_myStrPlan}/${_myStrPrognose}/${_myStrActual}`;
}


function aDashboardFormatPlanProgActHTML( plan, prog, act ) {
	
	let progColor = '#44aa44';
	let actColor = '#44aa44';
	if( typeof(plan) === 'string' ) { 	// A datetime value
		let planD = calendarStringToDate(plan);
		if( prog ) {
			progD = calendarStringToDate(prog);
			if( progD === null ) {
				prog = null;
			} else if( progD.getTime() > planD.getTime() ) {
				progColor = '#aa4444';
			}				
			if( prog !== null ) {
				prog = calendarDateToString(progD);
			}
		} 
		plan = calendarDateToString(planD);
		if( act !== null && act !== '' ) {
			let actD = calendarStringToDate(act);
			if( actD === null ) {
				act = null;
			} else if( actD.getTime() > planD.getTime() ) {
				actColor = '#aa4444';
			}
			if( act !== null ) {
				act = calendarDateToString(actD);
			}
		}
	} else {
		if( prog !== 0 && prog !== 'null' && prog !== '' ) {
			if( prog > plan ) {
				progColor = '#aa4444';
			}				
		} else {
			prog = null;
		}
		if( act !== 0 && act !== 'null' && act !== '' ) {
			if( act > plan ) {
				actColor = '#aa4444';
			}
		} else { 
			act = null; 
		}
	}
	let progHTML = ( prog ) ? `<span style='color:${progColor}'>${prog}</span>` : '-';
	let actHTML = ( act ) ? `<span style='color:${actColor}'>${act}</span>` : '-';
	let html = `${plan} /  ${progHTML} / ${actHTML}`;
	let title = `${plan} / ${prog} / ${act}`
	return [html, title];
}

var _aDashboardMenuItems = null;

function aDashboardMenuInit() {	
	if( _aDashboardMenuItems === null ) {
		_aDashboardMenuItems = document.querySelectorAll('[data-dashboardmenu]');
	}		
}

function aDashboardMenuSelect( selectedElement, menuElement=null ) {
	let items=null;
	if( menuElement === null ) {
		menuElement = selectedElement.getAttribute('data-dashboardmenu');
	}
	if( menuElement === null )
		return;
	let menu = document.getElementById(menuElement);
	if( !menu )
		return;
	items = menu.querySelectorAll('span'); 
	if( !items )
		return;
	for( let i = 0 ; i < items.length ; i++ ) {
		items[i].className = '';		
	} 
	selectedElement.className = 'active';
}


function aDashboardGetScrollbarWidth() {

	let outer = document.createElement('div');
	outer.style.visibility = 'hidden';
	outer.style.overflow = 'scroll'; // forcing scrollbar to appear
	outer.style.msOverflowStyle = 'scrollbar'; // needed for WinJS apps
	document.body.appendChild(outer);

	const inner = document.createElement('div');
	outer.appendChild(inner);

	const scrollbarWidth = (outer.offsetWidth - inner.offsetWidth);

	outer.parentNode.removeChild(outer);
	return scrollbarWidth;
}


var aDashboardIsProblemCB = function (data, elem) {
	if( data.is_problem == 1 ) {
		elem.innerHTML = `<span class="glyphicon glyphicon-warning-sign"></span>&nbsp;&nbsp;${data.problem}`;
		elem.title = data.problem; 
	} else {
		elem.innerHTML = '<span class="glyphicon glyphicon-thumbs-up"></span>';
		elem.title = 'Everything is Ok, no problems...';
	} 
}


var aDashboardIsDeliveredCB = function(data, elem) { 
	elem.innerHTML=(data.is_delivered==1)?'<span class="glyphicon glyphicon-ok"></span>':'<span class="glyphicon glyphicon-hourglass"></span>';
	elem.title=(data.is_delivered==1)?'Delivered ok':'Pending...';
}


var aDashboardHelperFormatUserDetails = function( user ) {
	let d = `<div align=center><b><mark>${user.name}</mark></b><br/><br/></div>`;
	d += `<span class="glyphicon glyphicon-envelope"></span>: <i>${user.email}</i><br/>`;
	if( 'balance' in user ) {
		d += `<span class="glyphicon glyphicon-piggy-bank"></span>&nbsp;${_myStrBalance}: `+
			`<b>${user.balance}</b>&nbsp;${_myConstCurrencyUnitHTML}<br/>`;

		if( 'deposit' in user ) {
			d += `${_myStrDeposit}: <b>${user.deposit}</b>&nbsp;${_myConstCurrencyUnitHTML}</br>`;	
			if( 'deposit_comment' in user ) {
				d += `${_myStrDepositComment}: <i>${user.deposit_comment}</i></br>`;	
			}
		}

		if( user.is_suspended_for_supply ) {
			d += '<span class="glyphicon glyphicon-shopping-cart"></span><span class="glyphicon glyphicon-thumbs-down"></span>';
			d += ` ${_myStrSuspended}`;
		} else {
			d += '<span class="glyphicon glyphicon-shopping-cart"></span><span class="glyphicon glyphicon-thumbs-up"></span>';
			d += ` ${_myStrNotSuspended}`;
		}
		d += '<br>';
	}
	if( 'farm_admin' in user ) {
		if( user.farm_admin > 0 ) {
			;
		}
	}
	if( user.icon ) {
		//d += `<img src='data:image/jpeg;base64,${user.icon}' height=80/><br/>`;
		d += `<img src='/scripts/icon.php?t=users&i=${user.id}' height=80/><br/>`;
	} 
	d += `Id: <b>${user.id}</b><br/>`;
	return d;
}
