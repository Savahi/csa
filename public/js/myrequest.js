
var _myRequestTimeout = null;

function myRequestSetErrorMessage( msg=null ) {
	if( _myRequestTimeout !== null ) {
		clearTimeout(_myRequestTimeout);
		_myRequestTimeout = null;
		if( msg !== null ) {
			myPopupDivShow("", msg, { mode:1, size:'s' } );
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


function myRequest(url, cb) {

	if( _myRequestTimeout === null ) {
		_myRequestTimeout = setTimeout( 
			function() { 
				_myRequestTimeout = null;			
				myPopupDivShow( "Please, wait...", "Please wait while loading the data...", { size:'s', mode:0 } );
			}, 250
		);
	}

	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 ) {
			if( this.status == 200) {
				let responseParsed = null;
				try {
					responseParsed= JSON.parse(this.responseText);
				} catch(e) {
					responseParsed = null;
					myRequestSetErrorMessage("ERROR!");
					return;
				}					 	
				myRequestSetErrorMessage();
     			cb(responseParsed);
	    	} else {
				responseParsed = null;
				myRequestSetErrorMessage("ERROR!");
				return;
			}
		}		
	};
	xhttp.open("GET", url, true);
	xhttp.send();
}

