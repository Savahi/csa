
var _aDashboardDataEditWindowOriginalIcon = '';
var _aDashboardDataEditWindowOriginalIconIsEmpty = true;
var _aDashboardDataEditWindowOriginalIconWasChanged = false;
var _aDashboardDataEditWindowOriginalIconWasDeleted = false;
var _aDashboardDataEditWindowIcon=null;
var _aDashboardDataEditWindowIconLabel=null;
var _aDashboardDataEditWindowIconReset=null;
var _aDashboardDataEditWindowIconDelete=null;
var _aDashboardDataEditWindowIconPreview=null;

function aInitDashboardDataEditWindowIcon(icon='') {

	let iconContainer = document.createElement('div');

	_aDashboardDataEditWindowIcon = document.createElement('input');
	_aDashboardDataEditWindowIcon.id = 'aDashboardDataEditWindowIcon';
	_aDashboardDataEditWindowIcon.type = 'file';
	_aDashboardDataEditWindowIcon.name = 'aDashboardIcon';
	_aDashboardDataEditWindowIcon.value = '';
	_aDashboardDataEditWindowIcon.onchange = function() { aDashboardDataEditWindowChangeIcon(); }

	_aDashboardDataEditWindowIconLabel = document.createElement("Label");
	_aDashboardDataEditWindowIconLabel.id = 'aDashboardDataEditWindowIconLabel';
	_aDashboardDataEditWindowIconLabel.htmlFor = 'aDashboardDataEditWindowIcon';
	_aDashboardDataEditWindowIconLabel.innerHTML = "&nbsp;&#8681;&nbsp;";

	_aDashboardDataEditWindowIconReset = document.createElement("div");
	_aDashboardDataEditWindowIconReset.id = 'aDashboardDataEditWindowIconReset';
	_aDashboardDataEditWindowIconReset.innerHTML = "&#8630;";
	_aDashboardDataEditWindowIconReset.onclick = function(e) { aDashboardDataEditWindowResetIcon(); };
	_aDashboardDataEditWindowIconDelete = document.createElement("div");
	_aDashboardDataEditWindowIconDelete.id = 'aDashboardDataEditWindowIconDelete';
	_aDashboardDataEditWindowIconDelete.innerHTML = "&#10006;";
	_aDashboardDataEditWindowIconDelete.onclick = function(e) { aDashboardDataEditWindowDeleteIcon(); };

	_aDashboardDataEditWindowIconPreview = document.createElement('img');
	_aDashboardDataEditWindowIconPreview.id = 'aDashboardDataEditWindowIconPreview';

	iconContainer.appendChild(_aDashboardDataEditWindowIcon);
	iconContainer.appendChild(_aDashboardDataEditWindowIconPreview);
	iconContainer.appendChild(_aDashboardDataEditWindowIconLabel);
	iconContainer.appendChild(_aDashboardDataEditWindowIconReset);
	iconContainer.appendChild(_aDashboardDataEditWindowIconDelete);
	
	aDashboardDataEditWindowSetOriginalIcon(icon);

	return iconContainer;
}

function aDashboardDataEditWindowIconValue() {
	return _aDashboardDataEditWindowIcon.files[0];
}


function aDashboardDataEditWindowIsIconChanged() {
	return _aDashboardDataEditWindowOriginalIconWasChanged;
}

function aDashboardDataEditWindowIsIconDeleted() {
	return _aDashboardDataEditWindowOriginalIconWasDeleted;
}


function aDashboardDataEditWindowSetOriginalIcon( icon='' ) {
	if( icon === null || icon === '' ) {
		_aDashboardDataEditWindowOriginalIcon = _myConstEmptyIcon; //_aDashboardDataEditWindowIconEmpty;
		_aDashboardDataEditWindowOriginalIconIsEmpty = true;	
	} else {
		_aDashboardDataEditWindowOriginalIcon = icon;
		_aDashboardDataEditWindowOriginalIconIsEmpty = false;	
	}
	if( _aDashboardDataEditWindowOriginalIconIsEmpty ) {
		aDashboardDataEditWindowSetIconPreview( _myConstEmptyIcon /*_aDashboardDataEditWindowIconEmpty*/ );
	} else {
		aDashboardDataEditWindowSetIconPreview( icon );
	}
	_aDashboardDataEditWindowOriginalIconWasChanged = false;
	_aDashboardDataEditWindowOriginalIconWasDeleted = false;
}

function aDashboardDataEditWindowDeleteIcon() {
	_aDashboardDataEditWindowIcon.value = '';
	aDashboardDataEditWindowSetIconPreview(_myConstEmptyIcon/*_aDashboardDataEditWindowIconEmpty*/);
	if( !_aDashboardDataEditWindowOriginalIconIsEmpty ) {
		_aDashboardDataEditWindowOriginalIconWasChanged = true;
		_aDashboardDataEditWindowOriginalIconWasDeleted = true;
	}
}


function aDashboardDataEditWindowResetIcon() {
	_aDashboardDataEditWindowIcon.value = '';
	aDashboardDataEditWindowSetIconPreview(_aDashboardDataEditWindowOriginalIcon);
	_aDashboardDataEditWindowOriginalIconWasChanged = false;
	_aDashboardDataEditWindowOriginalIconWasDeleted = false;
}


function aDashboardDataEditWindowChangeIcon() {
	if( _aDashboardDataEditWindowIcon.value === '' ) {
		if( _aDashboardDataEditWindowOriginalIconIsEmpty ) { 
			aDashboardDataEditWindowSetIconPreview(_myConstEmptyIcon/*_aDashboardDataEditWindowIconEmpty*/); 
		} else {
			aDashboardDataEditWindowSetIconPreview(_aDashboardDataEditWindowOriginalIcon); 
		}
		_aDashboardDataEditWindowOriginalIconWasChanged = false;
	} else {
		aDashboardDataEditWindowSetIconPreview();
		_aDashboardDataEditWindowOriginalIconWasChanged = true;
	}
	_aDashboardDataEditWindowOriginalIconWasDeleted = false;
}


function aDashboardDataEditWindowSetIconPreview( icon=null ) {
	let iconPreview = document.getElementById('aDashboardIconPreview');
	if( icon === null ) {
		let iconChosen = false;
		if (_aDashboardDataEditWindowIcon.files ) {
			if( _aDashboardDataEditWindowIcon.files[0]) {
				let reader = new FileReader();
    			reader.onload = function(e) {
      				_aDashboardDataEditWindowIconPreview.src = reader.result;
					_aDashboardDataEditWindowIconPreview.style.display = 'inline';
	    		}
    			reader.readAsDataURL(_aDashboardDataEditWindowIcon.files[0]);
				iconChosen = true;
			}
		}
		if( !iconChosen ) {
			_aDashboardDataEditWindowIconPreview.src = "data:image/jpg;base64, " + _myConstEmptyIcon; //_aDashboardDataEditWindowIconEmpty; // no-image icon
		}
	} else {
		_aDashboardDataEditWindowIconPreview.src = "data:image/jpg;base64, " + icon;
	}
}
